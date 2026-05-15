<?php

namespace App\Filament\Constructor\Blocks;

use App\Services\ImageService;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media;
use CubeAgency\FilamentConstructor\Constructor\Blocks\BlockRenderer;
use Filament\Forms\Components\TextInput;

class HeroBlock extends BlockRenderer
{
    public function name(): string
    {
        return 'hero';
    }

    public function title(): string
    {
        return __('forms.field_labels.hero');
    }

    public function schema(): array
    {
        return [
            CuratorPicker::make('image')
                ->label(__('forms.field_labels.image'))
                ->directory('pages')
                ->limitToDirectory()
                ->rules('required'),
            TextInput::make('image_alt')
                ->label(__('forms.field_labels.image_alt')),
            TextInput::make('title')
                ->label(__('forms.field_labels.title'))
                ->required(),
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

        return $this->view($block, [
            'mediaData' => $imageData,
            'title' => $block->data->title ?? '',
        ])->render();
    }
}
