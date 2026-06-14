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

            $slug = $file->getFilenameWithoutExtension();
            $imagePath = public_path('images/' . $slug . '.jpg');

            $recipes[] = [
                'fileName' => $file->getFilename(),
                'slug' => $slug,
                'imageUrls' => $this->getRecipeImageUrls($slug),
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


    /**
     * Retrieves a sorted list of image URLs for a given recipe slug.
     *
     * @param string $slug The unique identifier for the recipe used to locate its corresponding image files.
     * @return array An array of image URLs matching the specified recipe slug, or an empty array if no images are found.
     */
    private function getRecipeImageUrls(string $slug): array
    {
        $imagesDirectory = public_path('images');

        if (! File::isDirectory($imagesDirectory)) {
            return [];
        }

        $imageUrls = [];

        foreach (File::glob($imagesDirectory . DIRECTORY_SEPARATOR . $slug . '*.jpg') as $imagePath) {
            $imageUrls[] = asset('images/' . basename($imagePath));
        }

        sort($imageUrls);

        return $imageUrls;
    }
}
