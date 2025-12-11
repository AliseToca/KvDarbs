<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */

    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            'min:8',                     // at least 8 characters
            'regex:/[a-z]/',             // at least one lowercase
            'regex:/[A-Z]/',             // at least one uppercase
            'regex:/[0-9]/',             // at least one number
            'regex:/[\W]/',              // at least one symbol
            'confirmed',
        ];
    }
}
