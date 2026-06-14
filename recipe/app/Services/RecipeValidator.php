<?php

namespace App\Services;

class RecipeValidator
{
    public function validate(array $recipe): array
    {
        $errors = [];

        $this->validateRequiredString($recipe, 'name', 'Recipe name', $errors);
        $this->validateRequiredString($recipe, 'description', 'Recipe description', $errors);
        $this->validateIngredients($recipe, $errors);
        $this->validateMassFields($recipe, $errors);
        $this->validateSteps($recipe, $errors);
        $this->validateExpectedTotalDefaultTimeMinutes($recipe, $errors);

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    public function isValid(array $recipe): bool
    {
        $result = $this->validate($recipe);

        return $result['valid'];
    }

    private function validateRequiredString(array $data, string $field, string $label, array &$errors): void
    {
        if (! array_key_exists($field, $data)) {
            $errors[] = $label . ' is missing.';

            return;
        }

        if (! is_string($data[$field]) || trim($data[$field]) === '') {
            $errors[] = $label . ' must be a non-empty string.';
        }
    }

    private function validateIngredients(array $recipe, array &$errors): void
    {
        if (! array_key_exists('ingredients', $recipe)) {
            $errors[] = 'Ingredients are missing.';

            return;
        }

        if (! is_array($recipe['ingredients'])) {
            $errors[] = 'Ingredients must be an array.';

            return;
        }

        if (count($recipe['ingredients']) === 0) {
            $errors[] = 'Ingredients must contain at least one item.';

            return;
        }

        foreach ($recipe['ingredients'] as $index => $ingredient) {
            if (! is_string($ingredient) || trim($ingredient) === '') {
                $errors[] = 'Ingredient at position ' . ($index + 1) . ' must be a non-empty string.';
            }
        }
    }

    private function validateMassFields(array $recipe, array &$errors): void
    {
        $this->validatePositiveNumber($recipe, 'defaultMainIngredientMassKg', 'Default main ingredient mass', $errors);
        $this->validatePositiveNumber($recipe, 'minimumMass', 'Minimum mass', $errors);
        $this->validatePositiveNumber($recipe, 'maximumMass', 'Maximum mass', $errors);

        if (
            isset($recipe['minimumMass'], $recipe['maximumMass'])
            && is_numeric($recipe['minimumMass'])
            && is_numeric($recipe['maximumMass'])
            && (float) $recipe['minimumMass'] > (float) $recipe['maximumMass']
        ) {
            $errors[] = 'Minimum mass must be less than or equal to maximum mass.';
        }

        if (
            isset($recipe['defaultMainIngredientMassKg'], $recipe['minimumMass'], $recipe['maximumMass'])
            && is_numeric($recipe['defaultMainIngredientMassKg'])
            && is_numeric($recipe['minimumMass'])
            && is_numeric($recipe['maximumMass'])
        ) {
            $defaultMass = (float) $recipe['defaultMainIngredientMassKg'];
            $minimumMass = (float) $recipe['minimumMass'];
            $maximumMass = (float) $recipe['maximumMass'];

            if ($defaultMass < $minimumMass || $defaultMass > $maximumMass) {
                $errors[] = 'Default main ingredient mass must be between minimum mass and maximum mass.';
            }
        }
    }

    private function validatePositiveNumber(array $data, string $field, string $label, array &$errors): void
    {
        if (! array_key_exists($field, $data)) {
            $errors[] = $label . ' is missing.';

            return;
        }

        if (! is_numeric($data[$field]) || (float) $data[$field] <= 0) {
            $errors[] = $label . ' must be a positive number.';
        }
    }

    private function validateSteps(array $recipe, array &$errors): void
    {
        if (! array_key_exists('steps', $recipe)) {
            $errors[] = 'Steps are missing.';

            return;
        }

        if (! is_array($recipe['steps'])) {
            $errors[] = 'Steps must be an array.';

            return;
        }

        if (count($recipe['steps']) === 0) {
            $errors[] = 'Steps must contain at least one step.';

            return;
        }

        foreach ($recipe['steps'] as $index => $step) {
            $stepNumber = $index + 1;

            if (! is_array($step)) {
                $errors[] = 'Step ' . $stepNumber . ' must be an object.';

                continue;
            }

            $this->validateRequiredString($step, 'name', 'Step ' . $stepNumber . ' name', $errors);
            $this->validateRequiredString($step, 'description', 'Step ' . $stepNumber . ' description', $errors);
            $this->validateOptionalTemperature($step, $stepNumber, $errors);
            $this->validateOptionalNonNegativeNumber($step, 'fixedMinutes', 'Step ' . $stepNumber . ' fixed minutes', $errors);
            $this->validateOptionalNonNegativeNumber($step, 'minutesPerKg', 'Step ' . $stepNumber . ' minutes per kilogram', $errors);
        }
    }

    private function validateOptionalTemperature(array $step, int $stepNumber, array &$errors): void
    {
        if (! array_key_exists('temperature', $step)) {
            return;
        }

        if ($step['temperature'] === null) {
            return;
        }

        if (! is_numeric($step['temperature'])) {
            $errors[] = 'Step ' . $stepNumber . ' temperature must be a number or null.';
        }
    }

    private function validateOptionalNonNegativeNumber(array $data, string $field, string $label, array &$errors): void
    {
        if (! array_key_exists($field, $data)) {
            return;
        }

        if ($data[$field] === null) {
            return;
        }

        if (! is_numeric($data[$field]) || (float) $data[$field] < 0) {
            $errors[] = $label . ' must be a number greater than or equal to zero, or null.';
        }
    }

    private function validateExpectedTotalDefaultTimeMinutes(array $recipe, array &$errors): void
    {
        if (! array_key_exists('expectedTotalDefaultTimeMinutes', $recipe)) {
            return;
        }

        if ($recipe['expectedTotalDefaultTimeMinutes'] === null) {
            return;
        }

        if (! is_numeric($recipe['expectedTotalDefaultTimeMinutes'])) {
            $errors[] = 'Expected total default time minutes must be a number or null.';
        }
    }
}
