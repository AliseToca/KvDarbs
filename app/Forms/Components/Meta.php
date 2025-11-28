<?php

namespace App\Forms\Components;

use CubeAgency\FilamentJson\Filament\Forms\Components\Json;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;

class Meta extends Component
{
    protected string $view = 'filament-forms::components.group';

    protected string $name;

    protected bool $translatable = false;

    public static function make(string $name): static
    {
        $static = app(static::class);
        $static->configure();
        $static->setName($name);

        return $static;
    }

    public function translatable(): Meta
    {
        $this->translatable = true;

        return $this;
    }

    public function getChildComponents(): array
    {
        if ($this->translatable) {
            return [
                Translate::make()->schema($this->getMetaFields())
            ];
        }

        return $this->getMetaFields();
    }

    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    protected function getMetaFields(): array
    {
        return [
            Json::make($this->name)
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('title'),
                            TagsInput::make('keywords'),
                        ]),
                    Grid::make()
                        ->schema([
                            Textarea::make('description')
                                ->rows(3),
                            FileUpload::make('image')
                                ->image()
                                ->imagePreviewHeight('64'),
                        ]),
                ])
        ];
    }
}
