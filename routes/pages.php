<?php

use App\Filament\Templates\ConstructorTemplate;
use App\Filament\Templates\CookiesTemplate;
use App\Filament\Templates\LanguageTemplate;
use App\Http\Controllers\ConstructorPageController;
use App\Http\Controllers\CookiesPageController;
use App\Http\Controllers\LanguagePageController;
use CubeAgency\FilamentPageManager\Models\Page;
use CubeAgency\FilamentPageManager\Services\PageRoutes;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Filament\Templates\RecipeTemplate;
use App\Filament\Templates\HouseholdTemplate;
use App\Http\Controllers\HouseholdController;
use App\Filament\Templates\ShoppingListTemplate;
use App\Http\Controllers\ShoppingListController;

Route::get('/', [LanguagePageController::class, 'index']);
Route::post('/save-selected-cookies', CookiesPageController::class . '@saveSelectedCookies')->name('saveSelectedCookies');
Route::get('/accept-all-cookies', CookiesPageController::class . '@acceptAllCookies')->name('acceptAllCookies');
Route::get('/reject-all-cookies', CookiesPageController::class . '@rejectAllCookies')->name('rejectAllCookies');

PageRoutes::for(LanguageTemplate::class, static function (Page $page) {
    Route::get($page->getUri(), ['pageId' => $page->getKey()])
        ->uses([LanguagePageController::class, 'index'])
        ->name('index');
});

PageRoutes::for(ConstructorTemplate::class, function (Page $page) {
    Route::middleware('auth')->group(function () use ($page) {
        Route::get($page->getUri(), ['pageId' => $page->getKey()])
            ->uses([ConstructorPageController::class, 'index'])
            ->name('index');
    });
});

PageRoutes::for(CookiesTemplate::class, static function (Page $page) {
    Route::get($page->getUri(), ['pageId' => $page->getKey()])
        ->uses([CookiesPageController::class, 'index'])
        ->name('index');
});

PageRoutes::for(RecipeTemplate::class, static function (Page $page) {
    //Recepšu lapu katalogs
    Route::get($page->getUri(), ['pageId' => $page->getKey()])
        ->uses([RecipeController::class, 'index'])
        ->name('index');
    //Individuālā receptes lapa
    Route::get($page->getUri().'/{recipe:slug}', ['pageId' => $page->getKey()])
        ->uses([RecipeController::class, 'show'])
        ->name('show');
});

PageRoutes::for(HouseholdTemplate::class, static function (Page $page) {
    // Mājsaimniecības lapa nepiesaistītiem lietotājiem
    Route::get($page->getUri(), ['pageId' => $page->getKey()])
        ->uses([HouseholdController::class, 'index'])
        ->name('index');

    // Mājsaimniecības lapa ar lietotāja mājsaimniecību
    Route::get($page->getUri().'/{user:username}', ['pageId' => $page->getKey()])
        ->uses([HouseholdController::class, 'show'])
        ->name('show');
});

PageRoutes::for(ShoppingListTemplate::class, static function (Page $page) {
    Route::get($page->getUri(), ['pageId' => $page->getKey()])
        ->uses([ShoppingListController::class, 'show'])
        ->name('show');
});
