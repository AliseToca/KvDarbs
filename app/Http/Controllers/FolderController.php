<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FolderController extends Controller
{
    public function index(Request $request): Response
    {
        $folders = $request->user()->folders()->with('recipes')->latest()->get();

        return Inertia::render('Folder/Index', [
            'folders' => $folders,
        ]);

    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $request->user()->folders()->create($validated);

        return redirect()->back()->with('success', 'Recepšu saraksts veiksmīgi izveidots');
    }
}
