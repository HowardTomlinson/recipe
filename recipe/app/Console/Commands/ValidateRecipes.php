<?php

namespace App\Console\Commands;

use App\Services\RecipeValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ValidateRecipes extends Command
{
    protected $signature = 'recipes:validate';

    protected $description = 'Validate all recipe JSON files in the recipes directory';

    public function handle(RecipeValidator $recipeValidator): int
    {
        $recipesDirectory = base_path('recipes');

        if (! File::isDirectory($recipesDirectory)) {
            $this->error('Recipes directory does not exist: ' . $recipesDirectory);

            return self::FAILURE;
        }

        $recipeFiles = File::files($recipesDirectory);

        if (count($recipeFiles) === 0) {
            $this->warn('No recipe files found.');

            return self::SUCCESS;
        }

        $hasInvalidRecipes = false;

        foreach ($recipeFiles as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $fileName = $file->getFilename();
            $contents = File::get($file->getPathname());
            $recipe = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($recipe)) {
                $hasInvalidRecipes = true;

                $this->line('<fg=red>Invalid</> ' . $fileName);
                $this->line('  - Invalid JSON: ' . json_last_error_msg());

                continue;
            }

            $result = $recipeValidator->validate($recipe);

            if ($result['valid']) {
                $this->line('<fg=green>Valid</>   ' . $fileName);

                continue;
            }

            $hasInvalidRecipes = true;

            $this->line('<fg=red>Invalid</> ' . $fileName);

            foreach ($result['errors'] as $error) {
                $this->line('  - ' . $error);
            }
        }

        return $hasInvalidRecipes ? self::FAILURE : self::SUCCESS;
    }
}
