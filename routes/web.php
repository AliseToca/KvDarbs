<?php
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LocalOnly;
use App\Http\Controllers\UI\StyleGuidePageController;
use App\Http\Controllers\UI\ComponentPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdProductController;

//Route::get('/login', function () {
//    return redirect(route('filament.admin.auth.login'));
//})->name('login');

//Viesa adresācija
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

//Reģistrēta lietotāja adresācija
Route::middleware('auth')->group(function(){
    //Mājsaimniecības izveide
    Route::post('/households', [HouseholdController::class, 'store'])
        ->name('households.store');
    Route::post('/household-products', [HouseholdProductController::class, 'store'])
        ->name('household-products.store');
    Route::put('/household-products/{householdProduct}', [HouseholdProductController::class, 'update'])
        ->name('household-products.update');
});

Route::middleware(LocalOnly::class)->group(function () {
    Route::get('/ui-library', [StyleGuidePageController::class, 'index']);

    Route::controller(ComponentPageController::class)->group(function () {
        Route::get('/ui-library/components', 'index')->name('components');
        Route::get('/ui-library/components/get-demo-modal', 'getDemoModal')->name('components.get-demo-modal');
    });
});
