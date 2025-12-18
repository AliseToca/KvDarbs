<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class Settings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return __('resources.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.settings.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.settings.plural');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Analytics')
                            ->schema([
                                TextInput::make('google_tag_manager_id'),
                                TextInput::make('google_analytics_id'),
                            ]),
                    ])
            ]);
    }
}
