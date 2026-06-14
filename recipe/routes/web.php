<?php

use App\Services\RecipeLoader;
use Illuminate\Support\Facades\Route;

Route::get('/', function (RecipeLoader $recipeLoader) {
    return view('recipes.index', [
        'recipes' => $recipeLoader->loadValidRecipes(),
    ]);
});
