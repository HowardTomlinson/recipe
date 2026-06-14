<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RecipeLoader
{
    public function __construct(
        private RecipeValidator $recipeValidator
    ) {
    }
    public function loadValidRecipes(): array
    {
        $recipesDirectory = base_path('recipes');

        if (! File::isDirectory($recipesDirectory)) {
            return [];
        }

        $recipes = [];

        foreach (File::files($recipesDirectory) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $contents = File::get($file->getPathname());
            $recipe = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($recipe)) {
                continue;
            }

            if (! $this->recipeValidator->isValid($recipe)) {
                continue;
            }

            $recipes[] = [
                'fileName' => $file->getFilename(),
                'slug' => $file->getFilenameWithoutExtension(),
                'data' => $recipe,
            ];
        }

        usort($recipes, function (array $firstRecipe, array $secondRecipe): int {
            return strcmp(
                $firstRecipe['data']['name'] ?? $firstRecipe['fileName'],
                $secondRecipe['data']['name'] ?? $secondRecipe['fileName']
            );
        });

        return $recipes;
    }
}
