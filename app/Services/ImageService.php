<?php

namespace App\Services;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Spatie\Glide\GlideImage;

class ImageService
{
    // Fallback configuration values (see config/laravel-glide.php)
    protected const array IMAGE_VARIATIONS = [320, 640, 960, 1280, 1920, 3840];
    protected const int FALLBACK_WIDTH = 1280;
    protected const array FORMAT_QUALITY = [
        'webp' => 90,
        'jpg' => 90,
        'jpeg' => 90,
        'png' => 90,
    ];

    protected const FALLBACK_QUALITY = 90;

    protected array $imageSizes;

    protected array $formatQualityMap;

    protected int $fallbackWidth;


    protected string $outputDisk;

    public function __construct()
    {
        $this->imageSizes = config('laravel-glide.image_variations', self::IMAGE_VARIATIONS);
        $responsive = config('laravel-glide.responsive', []);
        $this->fallbackWidth = $responsive['fallback_width'] ?? self::FALLBACK_WIDTH;
        $this->formatQualityMap = $responsive['quality'] ?? self::FORMAT_QUALITY;
        $this->outputDisk = config('laravel-glide.output_disk_name');
    }

    public function getImageAndSourceSetUrls($image): array
    {
        $dimensions = $this->getOriginalDimensions($image);
        $originalWidth = $dimensions['width'];

        return [
            'src' => $this->getImageUrl($image, 'webp', $originalWidth),
            'srcset' => $this->getSourceSetUrls($image, $originalWidth),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
        ];
    }

    public function getSourceSetUrls($image, ?int $originalWidth = null): string
    {
        if (!$image) {
            return '';
        }

        $sourceSetSizes = $this->getSourceSetSizes($originalWidth ?? $this->getOriginalWidth($image));

        return $sourceSetSizes->map(function ($size) use ($image) {
            if ($image instanceof Media) {
                $params = ['w' => $size, 'fm' => 'webp'];
                return $image->getSignedUrl($params) . ' ' . $size . 'w';
            }

            if (is_string($image)) {
                return $this->getProcessedAssetUrl($image, $size) . ' ' . $size . 'w';
            }

            return null;
        })->implode(', ');
    }

    public function getImageUrl($image, string $format = 'webp', ?int $originalWidth = null): ?string
    {
        if (!$image) {
            return null;
        }

        $width = $originalWidth ?? $this->getOriginalWidth($image);
        $fallbackWidth = $width ? min($this->fallbackWidth, $width) : $this->fallbackWidth;

        if ($image instanceof Media) {
            return $image->getSignedUrl(['w' => $fallbackWidth, 'fm' => $format]);
        }

        if (is_string($image)) {
            return $this->getProcessedAssetUrl($image, $fallbackWidth, $format);
        }

        return null;
    }

    protected function getProcessedAssetUrl(string $imagePath, int $width, string $format = 'webp'): ?string
    {
        $sourcePath = $this->getAssetSourcePath($imagePath);

        if (!$sourcePath) {
            return null;
        }

        $disk = Storage::disk('public');
        $processedPath = $this->getProcessedImagePath($imagePath, $width, $format);

        if (!$disk->exists($processedPath)) {
            $this->createProcessedImage($sourcePath, $disk->path($processedPath), $width, $format);
        }

        return $disk->exists($processedPath) ? asset('storage/' . $processedPath) : null;
    }

    protected function getAssetSourcePath(string $imagePath): ?string
    {
        $imagePath = ltrim(str_replace(['..', '\\'], ['', '/'], $imagePath), '/');

        return collect([
            resource_path("assets/images/{$imagePath}"),
            public_path($imagePath),
            Storage::disk($this->outputDisk)->path($imagePath)
        ])
            ->map(fn($path) => realpath($path))
            ->filter()
            ->first();
    }

    protected function getProcessedImagePath(string $originalPath, int $width, string $format): string
    {
        $originalPath = trim(str_replace(['..', '\\'], ['', '/'], $originalPath), '/');
        $info = pathinfo($originalPath);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'] . '/';
        $file = $info['filename'] ?? 'image';

        return 'assets/' . $dir . $file . '/' . $width . 'w.' . $format;
    }

    protected function getOriginalWidth($image): ?int
    {
        return $this->getOriginalDimensions($image)['width'];
    }

    protected function getOriginalHeight($image): ?int
    {
        return $this->getOriginalDimensions($image)['height'];
    }

    protected function getOriginalDimensions($image): array
    {
        $dimensions = ['width' => null, 'height' => null];

        if ($image instanceof Media) {
            $dimensions['width'] = $image->width ?? null;
            $dimensions['height'] = $image->height ?? null;

            return $dimensions;
        }

        if (is_string($image)) {
            $sourcePath = $this->getAssetSourcePath($image);
            if ($sourcePath && is_file($sourcePath)) {
                [$width, $height] = getimagesize($sourcePath) ?: [null, null];
                $dimensions['width'] = $width;
                $dimensions['height'] = $height;
            }
        }

        return $dimensions;
    }

    protected function getSourceSetSizes(?int $originalWidth)
    {
        $baseWidths = collect($this->imageSizes);

        return $baseWidths
            ->unique()
            ->sort()
            ->values()
            ->when($originalWidth !== null, function ($collection) use ($originalWidth) {
                $filtered = $collection->filter(fn($w) => $w <= $originalWidth);

                return $filtered->when(
                    ! $filtered->contains($originalWidth) && $originalWidth < min($this->imageSizes),
                    fn($col) => $col->push($originalWidth)->sort()->values()
                );
            });
    }

    protected function createProcessedImage(string $sourcePath, string $outputPath, int $width, string $format = 'webp'): void
    {
        try {
            if (!is_dir($dir = dirname($outputPath))) {
                mkdir($dir, 0750, true);
            }

            $quality = $this->formatQualityMap[$format] ?? self::FALLBACK_QUALITY;

            $params = [
                'w' => $width,
                'fm' => $format,
                'q' => $quality,
            ];

            GlideImage::create($sourcePath)
                ->modify($params)
                ->save($outputPath);

            if (($t = filemtime($sourcePath)) !== false) {
                touch($outputPath, $t);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
