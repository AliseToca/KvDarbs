<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\User;
use App\Services\BreadcrumbService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FolderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    public function index(Request $request): Response
    {
        $folders = $request->user()->folders()->with(['recipes', 'user:id,username'])->latest()->get();

        return Inertia::render('Folder/Index', [
            'folders' => $folders,
        ]);
    }

    public function show(User $user, Folder $folder): Response
    {
        $this->authorize('view', $user, $folder);

        $folder->load('recipes');

        return Inertia::render('Folder/Show', [
            'folder' => $folder,
            'breadcrumbs' => $this->breadcrumbService->forFolderShow($user, $folder),
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
