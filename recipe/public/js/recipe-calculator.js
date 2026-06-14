const recipeSelect = document.getElementById('recipe-select');
const recipeName = document.getElementById('recipe-name');
const recipeDescription = document.getElementById('recipe-description');
const ingredientsList = document.getElementById('ingredients-list');
const massSlider = document.getElementById('mass-slider');
const massInput = document.getElementById('mass-input');
const stepsList = document.getElementById('steps-list');
const totalTime = document.getElementById('total-time');

function getSelectedRecipe() {
    return window.recipes.find(function (recipe) {
        return recipe.slug === recipeSelect.value;
    });
}

function roundMinutes(value) {
    return Math.round(value);
}

function clampMass(value, recipeData) {
    const minimumMass = Number(recipeData.minimumMass);
    const maximumMass = Number(recipeData.maximumMass);

    if (value < minimumMass) {
        return minimumMass;
    }

    if (value > maximumMass) {
        return maximumMass;
    }

    return value;
}

function formatMass(value) {
    return Number(value).toFixed(1);
}

function calculateStepTime(step, massKg) {
    let stepTime = 0;
    let hasTime = false;

    if (step.fixedMinutes !== undefined && step.fixedMinutes !== null) {
        stepTime += Number(step.fixedMinutes);
        hasTime = true;
    }

    if (step.minutesPerKg !== undefined && step.minutesPerKg !== null) {
        stepTime += roundMinutes(massKg * Number(step.minutesPerKg));
        hasTime = true;
    }

    return {
        hasTime: hasTime,
        minutes: stepTime
    };
}

function renderIngredients(recipeData) {
    ingredientsList.innerHTML = '';

    recipeData.ingredients.forEach(function (ingredient) {
        const item = document.createElement('li');
        item.textContent = ingredient;
        ingredientsList.appendChild(item);
    });
}

function renderSteps(recipeData, massKg) {
    stepsList.innerHTML = '';

    let calculatedTotalTime = 0;

    recipeData.steps.forEach(function (step, index) {
        const calculatedStepTime = calculateStepTime(step, massKg);

        if (calculatedStepTime.hasTime) {
            calculatedTotalTime += calculatedStepTime.minutes;
        }

        const item = document.createElement('li');

        const heading = document.createElement('h3');
        heading.textContent = 'Step ' + (index + 1) + ': ' + step.name;
        item.appendChild(heading);

        const description = document.createElement('p');
        description.textContent = step.description;
        item.appendChild(description);

        const temperature = document.createElement('p');

        if (step.temperature === undefined || step.temperature === null) {
            temperature.textContent = 'Temperature: no oven temperature';
        } else {
            temperature.textContent = 'Temperature: ' + step.temperature + '°C';
        }

        item.appendChild(temperature);

        const time = document.createElement('p');

        if (calculatedStepTime.hasTime) {
            time.textContent = 'Time: ' + calculatedStepTime.minutes + ' minutes';
        } else {
            time.textContent = 'Time: no timed duration';
        }

        item.appendChild(time);

        stepsList.appendChild(item);
    });

    totalTime.textContent = calculatedTotalTime + ' minutes';
}

function renderSelectedRecipe() {
    const selectedRecipe = getSelectedRecipe();

    if (! selectedRecipe) {
        return;
    }

    const recipeData = selectedRecipe.data;
    const defaultMass = Number(recipeData.defaultMainIngredientMassKg);

    recipeName.textContent = recipeData.name;
    recipeDescription.textContent = recipeData.description;

    massSlider.min = recipeData.minimumMass;
    massSlider.max = recipeData.maximumMass;
    massSlider.value = defaultMass;

    massInput.min = recipeData.minimumMass;
    massInput.max = recipeData.maximumMass;
    massInput.value = formatMass(defaultMass);

    renderIngredients(recipeData);
    renderSteps(recipeData, defaultMass);
}

function updateMass(newMass) {
    const selectedRecipe = getSelectedRecipe();

    if (! selectedRecipe) {
        return;
    }

    const recipeData = selectedRecipe.data;
    const clampedMass = clampMass(Number(newMass), recipeData);
    const formattedMass = formatMass(clampedMass);

    massSlider.value = formattedMass;
    massInput.value = formattedMass;

    renderSteps(recipeData, clampedMass);
}

recipeSelect.addEventListener('change', function () {
    renderSelectedRecipe();
});

massSlider.addEventListener('input', function () {
    updateMass(massSlider.value);
});

massInput.addEventListener('change', function () {
    updateMass(massInput.value);
});

renderSelectedRecipe();
