<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Recipe;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FolderRecipeController extends Controller
{
    use AuthorizesRequests;

    public function store(Folder $folder, Recipe $recipe): RedirectResponse
    {
        $this->authorize('update', $folder);

        if ($folder->recipes()->where('recipe_id', $recipe->id)->exists()) {
            return redirect()->back()->with('info', 'Recepte jau ir pievienota šim sarakstam');
        }

        $folder->recipes()->attach($recipe->id);

        return redirect()->back()->with('success', 'Recepte veiksmīgi pievienota sarakstam');
    }

    public function destroy(Folder $folder, Recipe $recipe): RedirectResponse
    {
        $this->authorize('update', $folder);

        $folder->recipes()->detach($recipe->id);

        return redirect()->back()->with('success', 'Recepte veiksmīgi noņemta no saraksta');
    }
}
