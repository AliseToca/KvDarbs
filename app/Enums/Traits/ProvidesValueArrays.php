<?php

namespace App\Enums\Traits;

trait ProvidesValueArrays
{
    public static function asValueArray(?string $additionalValue = null): array
    {
        $valueArray = [];
        $reflector = new \ReflectionClass(self::class);

        foreach ($reflector->getConstants() as $constant) {
            $valueArray[] = $constant;
        }

        if ($additionalValue) {
            $valueArray[] = $additionalValue;
        }

        return $valueArray;
    }

    public static function asSelectArray(?string $additionalValue = null): array
    {
        $valueArray = [];
        $reflector = new \ReflectionClass(self::class);

        foreach ($reflector->getConstants() as $constant) {
            $valueArray[$constant] = $constant;
        }

        if ($additionalValue) {
            $valueArray[$additionalValue] = $additionalValue;
        }

        return $valueArray;
    }
}
