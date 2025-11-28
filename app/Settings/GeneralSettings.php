<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $google_tag_manager_id;
    public ?string $google_analytics_id;


    public static function group(): string
    {
        return 'general';
    }
}
