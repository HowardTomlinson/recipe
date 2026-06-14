<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Cooking Times</title>
    <link rel="stylesheet" href="{{ asset('css/recipe.css') }}">
</head>
<body>
<main>
    <h1>Recipe Cooking Times</h1>

    @if (count($recipes) === 0)
        <p>No recipes available</p>
    @else
        <section>
            <label for="recipe-select">Choose a recipe</label>
            <select id="recipe-select">
                @foreach ($recipes as $recipe)
                    <option value="{{ $recipe['slug'] }}">
                        {{ $recipe['data']['name'] }}
                    </option>
                @endforeach
            </select>
        </section>

        <section>
            <h2 id="recipe-name"></h2>
            <p id="recipe-description"></p>
        </section>

        <section>
            <h2>Ingredients</h2>
            <ul id="ingredients-list"></ul>
        </section>

        <section>
            <h2>Main ingredient mass</h2>

            <div class="mass-control">
                <label for="mass-slider">Mass in kilograms</label>
                <input
                    type="range"
                    id="mass-slider"
                    step="0.1"
                >

                <label for="mass-input">Selected mass</label>
                <input
                    type="number"
                    id="mass-input"
                    step="0.1"
                >
            </div>
        </section>

        <section>
            <h2>Cooking steps</h2>
            <ol id="steps-list"></ol>
        </section>

        <section>
            <h2>Total time</h2>
            <p id="total-time"></p>
        </section>

        <section id="recipe-image-section" class="recipe-image-section" hidden>
            <h2>Pictures</h2>
            <div id="recipe-images" class="recipe-images"></div>
        </section>

        <script>
            window.recipes = @json($recipes);
        </script>
        <script src="{{ asset('js/recipe-calculator.js') }}"></script>
    @endif
</main>
</body>
</html>
