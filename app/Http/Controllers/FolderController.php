<?php

namespace App\Http\Controllers;

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
}
