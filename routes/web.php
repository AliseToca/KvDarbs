<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LocalOnly;
use App\Http\Controllers\UI\StyleGuidePageController;
use App\Http\Controllers\UI\ComponentPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdProductController;
use App\Http\Controllers\ReviewController;

//Route::get('/login', function () {
//    return redirect(route('filament.admin.auth.login'));
//})->name('login');

Route::get('/', function () {
    return redirect('/lv/');
});

//Viesa adresācija
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

//Reģistrēta lietotāja adresācijas
Route::middleware('auth')->group(function(){
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::post('/profile-delete', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::post('/households', [HouseholdController::class, 'store'])
        ->name('households.store');
    Route::put('/households/{household}', [HouseholdController::class, 'update'])
        ->name('households.update');

    Route::post('/household-products', [HouseholdProductController::class, 'store'])
        ->name('household-products.store');
    Route::put('/household-products/{householdProduct}', [HouseholdProductController::class, 'update'])
        ->name('household-products.update');
    Route::delete('/household-products/{householdProduct}', [HouseholdProductController::class, 'destroy'])
        ->name('household-products.destroy');

    Route::get('/recipe/create', [RecipeController::class, 'create']);
    Route::post('/recipe/store', [RecipeController::class, 'store'])
        ->name('recipes.store');

    Route::post('/recipes/{recipe:slug}/reviews', [ReviewController::class, 'store'])
        ->name('recipes.reviews.store');
});

Route::middleware(LocalOnly::class)->group(function () {
    Route::get('/ui-library', [StyleGuidePageController::class, 'index']);

    Route::controller(ComponentPageController::class)->group(function () {
        Route::get('/ui-library/components', 'index')->name('components');
        Route::get('/ui-library/components/get-demo-modal', 'getDemoModal')->name('components.get-demo-modal');
    });
});
