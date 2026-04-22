<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['slug'] = $user->username . '-' . Str::slug($data['name']);
        $data['user_id'] = $user->id;
        $data['content'] = $data['content'] ?? 'test';

        return $data;
    }
}
