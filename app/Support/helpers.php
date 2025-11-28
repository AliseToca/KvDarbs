<?php

use Illuminate\Support\Str;

if (!function_exists('input_error_id')) {
    function input_error_id($name, $value = null): string
    {
        $cleanName = Str::slug($name);
        $cleanValue = $value ? Str::slug($value) : '';

        return "error-{$cleanName}" . ($cleanValue ? "-{$cleanValue}" : '');
    }
}

if (!function_exists('rgba_from_color')) {
    function rgba_from_color(string $color, ?float $opacity = null): string
    {
        $opacity = $opacity ?? 1;

        return "rgba({$color}, {$opacity})";
    }
}
