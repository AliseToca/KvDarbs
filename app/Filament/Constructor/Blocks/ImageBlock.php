<?php

namespace App\Filament\Constructor\Blocks;

use App\Services\ImageService;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media;
use CubeAgency\FilamentConstructor\Constructor\Blocks\BlockRenderer;
use Filament\Forms\Components\TextInput;

class ImageBlock extends BlockRenderer
{
    public function name(): string
    {
        return 'image';
    }

    public function title(): string
    {
        return __('Image');
    }

    public function schema(): array
    {
        return [
            CuratorPicker::make('image')
                ->label('Image')
                ->directory('pages')
                ->limitToDirectory()
                ->rules('required'),
            TextInput::make('image_alt'),
        ];
    }

    public function html(object $block): string
    {
        $imageId = $block->data->image ?? null;

        if (!$imageId) {
            return '';
        }

        $media = Media::find($imageId);

        if (!$media) {
            return '';
        }

        $imageData = resolve(ImageService::class)->getImageAndSourceSetUrls($media);

        return $this->view($block, ['mediaData' => $imageData])->render();
    }
}
