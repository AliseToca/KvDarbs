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
use App\Http\Controllers\HouseholdEmailInviteController;
use App\Http\Controllers\HouseholdUserController;
use App\Http\Controllers\ShoppingListController;

Route::get('/', function () {
    return redirect('/lv/');
});

Route::get('/households/join-email/{token}', [HouseholdEmailInviteController::class, 'show'])
    ->name('households.invite.email.show');

//Viesa adresācija
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

//Reģistrēta lietotāja adresācijas
Route::middleware('auth')->group(function(){
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.updateAvatar');
    Route::post('/profile-delete', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::post('/households', [HouseholdController::class, 'store'])
        ->name('households.store');
    Route::put('/households/{household}', [HouseholdController::class, 'update'])
        ->name('households.update');
    Route::get('/households/{household}/edit', [HouseholdController::class, 'edit'])
        ->name('households.edit');
    Route::delete('/households/{household}/delete', [HouseholdController::class, 'destroy'])
        ->name('households.destroy');

    Route::patch('/households/{household}/users', [HouseholdUserController::class, 'update'])
        ->name('households.users.update');
    Route::delete('/households/{household}/users/leave', [HouseholdUserController::class, 'leave'])
        ->name('households.users.leave');
    Route::delete('/households/{household}/users/{user}', [HouseholdUserController::class, 'destroy'])
        ->name('households.users.destroy');

    Route::post('/households/{household}/invite/email', [HouseholdEmailInviteController::class, 'send'])
        ->name('households.invite.email.send');
    Route::delete('/households/{household}/invite/email/{invitation}', [HouseholdEmailInviteController::class, 'cancel'])
        ->name('households.invite.email.cancel');
    Route::post('/households/join-email/{token}', [HouseholdEmailInviteController::class, 'accept'])
        ->name('households.invite.email.accept');

    Route::post('/household-products', [HouseholdProductController::class, 'store'])
        ->name('household-products.store');
    Route::put('/household-products/{householdProduct}', [HouseholdProductController::class, 'update'])
        ->name('household-products.update');
    Route::delete('/household-products/{householdProduct}', [HouseholdProductController::class, 'destroy'])
        ->name('household-products.destroy');

    Route::get('/recipes/my', [RecipeController::class, 'myRecipes'])
        ->name('recipe.my');
    Route::get('/recipe/create', [RecipeController::class, 'create'])
        ->name('recipe.create');
    Route::get('/recipes/{recipe:slug}/edit', [RecipeController::class, 'edit'])
        ->name('recipes.edit');
    Route::post('/recipes/{recipe:slug}', [RecipeController::class, 'update'])
        ->name('recipes.update');
    Route::post('/recipe/store', [RecipeController::class, 'store'])
        ->name('recipes.store');
    Route::delete('/recipes/{recipe:slug}', [RecipeController::class, 'destroy'])
        ->name('recipe.delete');

    Route::post('/recipes/{recipe:slug}/reviews', [ReviewController::class, 'store'])
        ->name('recipes.reviews.store');

    Route::get('/shopping-list', [ShoppingListController::class, 'show'])
        ->name('shopping-list.show');
    Route::post('/shopping-list', [ShoppingListController::class, 'store'])
        ->name('shopping-list.store');
    Route::patch('/shopping-list/{shoppingList}', [ShoppingListController::class, 'toggle'])
        ->name('shopping-list.toggle');
    Route::delete('/shopping-list/{shoppingList}', [ShoppingListController::class, 'destroy'])
        ->name('shopping-list.destroy');

    Route::post('/shopping-list/{recipe}/add-from-recipe', [ShoppingListController::class, 'addFromRecipe'])
        ->name('shopping-list.add-from-recipe');
});

Route::middleware(LocalOnly::class)->group(function () {
    Route::get('/ui-library', [StyleGuidePageController::class, 'index']);

    Route::controller(ComponentPageController::class)->group(function () {
        Route::get('/ui-library/components', 'index')->name('components');
        Route::get('/ui-library/components/get-demo-modal', 'getDemoModal')->name('components.get-demo-modal');
    });
});
