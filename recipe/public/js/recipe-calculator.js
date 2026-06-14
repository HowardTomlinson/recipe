const recipeSelect = document.getElementById('recipe-select');
const recipeName = document.getElementById('recipe-name');
const recipeDescription = document.getElementById('recipe-description');
const ingredientsList = document.getElementById('ingredients-list');
const massSlider = document.getElementById('mass-slider');
const massInput = document.getElementById('mass-input');
const stepsList = document.getElementById('steps-list');
const totalTime = document.getElementById('total-time');
const recipeImageSection = document.getElementById('recipe-image-section');
const recipeImages = document.getElementById('recipe-images');
const safetyMessage = document.getElementById('safety-message');

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
        item.classList.add('step-card');

        const stepContent = document.createElement('div');
        stepContent.classList.add('step-content');

        const heading = document.createElement('h3');
        heading.textContent = 'Step ' + (index + 1) + ': ' + step.name;
        stepContent.appendChild(heading);

        const description = document.createElement('p');
        description.textContent = step.description;
        stepContent.appendChild(description);

        if (step.temperature !== undefined && step.temperature !== null) {
            const temperature = document.createElement('p');
            temperature.textContent = 'Temperature: ' + step.temperature + '°C';
            stepContent.appendChild(temperature);
        }

        if (calculatedStepTime.hasTime) {
            const time = document.createElement('p');
            time.classList.add('step-time');
            time.textContent = 'Time: ' + calculatedStepTime.minutes + ' minutes';
            stepContent.appendChild(time);
        }

        item.appendChild(stepContent);

        if (step.temperature !== undefined && step.temperature !== null) {
            const icon = document.createElement('div');
            icon.classList.add('step-icon', 'oven-icon');
            icon.setAttribute('aria-label', 'Oven temperature ' + step.temperature + ' degrees Celsius');

            const ovenTemperature = document.createElement('span');
            ovenTemperature.classList.add('oven-temperature');
            ovenTemperature.textContent = step.temperature; //  + '°C';

            icon.appendChild(ovenTemperature);
            item.appendChild(icon);
        }

        stepsList.appendChild(item);
    });

    totalTime.textContent = calculatedTotalTime + ' minutes';
}

function renderRecipeImages(selectedRecipe) {
    recipeImages.innerHTML = '';

    if (!selectedRecipe.imageUrls || selectedRecipe.imageUrls.length === 0) {
        recipeImageSection.hidden = true;

        return;
    }

    selectedRecipe.imageUrls.forEach(function (imageUrl, index) {
        const image = document.createElement('img');
        image.classList.add('recipe-image');
        image.src = imageUrl;
        image.alt = selectedRecipe.data.name + ' picture ' + (index + 1);

        recipeImages.appendChild(image);
    });

    recipeImageSection.hidden = false;
}

function renderSelectedRecipe() {
    const selectedRecipe = getSelectedRecipe();

    if (!selectedRecipe) {
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
    renderRecipeImages(selectedRecipe);
    renderSafetyMessage();

}

function renderSafetyMessage() {
    const messages = [
        'Proudly celebrating <strong>{days}</strong> days since our last fatal accident.',
        'Now serving roast dinners for <strong>{days}</strong> days without a catastrophic gravy incident.',
        'This kitchen has survived <strong>{days}</strong> days without anyone flambéing the curtains.',
        'Proudly operating for <strong>{days}</strong> days since the last suspicious oven noise.',
        'Officially <strong>{days}</strong> days since someone asked if smoke is meant to happen.',
        'This has been a velociraptor fatality-free kitchen for <strong>{days}</strong> days.'
    ];

    const days = Math.floor(Math.random() * 73) + 2;
    const selectedMessage = messages[Math.floor(Math.random() * messages.length)];

    safetyMessage.innerHTML = selectedMessage.replace('{days}', days);
}

function updateMass(newMass) {
    const selectedRecipe = getSelectedRecipe();

    if (!selectedRecipe) {
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
