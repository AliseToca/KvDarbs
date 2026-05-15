<?php

namespace App\Filament\Constructor\Blocks;

use App\Services\ImageService;
use CubeAgency\FilamentConstructor\Constructor\Blocks\BlockRenderer;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class HeroBlock extends BlockRenderer
{
    public function name(): string
    {
        return 'hero';
    }

    public function title(): string
    {
        return 'Hero';
    }

    public function schema(): array
    {
        return [
            TextInput::make('title')
                ->label('Title')
                ->required(),

            TextInput::make('subtitle')
                ->label('Subtitle'),

            TextInput::make('cta_label')
                ->label('Button text')
                ->default('Skatīt receptes'),

            TextInput::make('cta_url')
                ->label('Button URL')
                ->default('#'),

            FileUpload::make('image_main')
                ->label('Main image (center)')
                ->image()
                ->directory('hero')
                ->required(),

            FileUpload::make('image_top')
                ->label('Top card image')
                ->image()
                ->directory('hero'),

            FileUpload::make('image_bottom')
                ->label('Bottom card image')
                ->image()
                ->directory('hero'),
        ];
    }

    public function html(object $block): string
    {
        if (empty($block->data->image_main)) {
            return '';
        }

        return $this->view($block, [
            'title'       => $block->data->title ?? '',
            'subtitle'    => $block->data->subtitle ?? '',
            'ctaLabel'    => $block->data->cta_label ?? 'Skatīt receptes',
            'ctaUrl'      => $block->data->cta_url ?? '#',
            'imageMain'   => $block->data->image_main,
            'imageTop'    => $block->data->image_top ?? null,
            'imageBottom' => $block->data->image_bottom ?? null,
        ])->render();
    }
}
