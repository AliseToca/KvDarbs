<?php
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LocalOnly;
use App\Http\Controllers\UI\StyleGuidePageController;
use App\Http\Controllers\UI\ComponentPageController;
use Inertia\Inertia;

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');


Route::middleware(LocalOnly::class)->group(function () {
    Route::get('/ui-library', [StyleGuidePageController::class, 'index']);

    Route::controller(ComponentPageController::class)->group(function () {
        Route::get('/ui-library/components', 'index')->name('components');
        Route::get('/ui-library/components/get-demo-modal', 'getDemoModal')->name('components.get-demo-modal');
    });
});


Route::get('/test', function () {
    return Inertia::render('Welcome');
});


Route::get('/', function () {
    return Inertia::render('Home');
});
