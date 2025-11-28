<?php

namespace App\Enums\Cookies;

use App\Enums\Traits\ProvidesValueArrays;

class CookieGroupName
{
    use ProvidesValueArrays;

    public const MANDATORY = 'mandatory';
    public const ANALYTICS_TRACKER = 'analytics_tracker';
    public const MARKETING_TRACKER = 'marketing_tracker';
    public const STATISTICS_TRACKER = 'statistics_tracker';
}
