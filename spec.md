This project is to create a small web app, showing a limited number of cooking recipes. The user will be able to select which recipe, enter a single value for weight in decimal kilograms, and the site will calculate the sequence of cooking times in minutes and temperatures in Celsius.

The tech stack is laravel, and the single page will have javascript to perform the calculation on the page.

No database or schema is required, the definitions for the recipes will be held in a directory called recipes, and there will be one file per recipe, with the definitions to use.

# Recipe files

Recipes are stored as JSON files in a /recipes directory. Each file contains a recipe object with a name and an array of steps.

The recipe file fields will be:
- name
- description
- ingredients array
- defaultMainIngredientMassKg
- minimumMass
- maximumMass
- steps

Optional recipe fields:
- expectedTotalDefaultTimeMinutes

For each step, the fields will be:
- name
- description
- temperature (optional)
- fixedMinutes (optional)
- minutesPerKg (optional)


The Expected total time in minutes at the default mass, including fixed and variable times for all steps, in order to provide a test for calculation expectations. 


Example of the JSON for a recipe, this is one of the recipes to be included:

This will be a file called : roastchicken.json

{
"name": "Roast Chicken",
"description": "Delicious roast chicken",
"ingredients": ["chicken", "stuffing", "potatoes", "gravy"],
"defaultMainIngredientMassKg": 1.6,
"minimumMass": 1.0,
"maximumMass": 3.2,
"steps": [
{
"name": "Preheat",
"description": "Preheat the oven to the roast temperature",
"temperature": 220,
"fixedMinutes": 15
},
{
"name": "Initial roast",
"description": "Roast chicken at the higher temperature",
"temperature": 220,
"minutesPerKg": 20,
"fixedMinutes": 10
},
{
"name": "Main roast",
"description": "Turn down the temperature for the main roast",
"temperature": 180,
"fixedMinutes": 20
},
{
"name": "Resting time",
"description": "Remove from oven and allow to rest covered on the side.",
"temperature": null,
"fixedMinutes": 15
},
{
"name": "Gravy",
"description": "Make gravy",
"temperature": null,
"fixedMinutes": 0
}
],
"expectedTotalDefaultTimeMinutes": 92
}


There will also need to be example recipes for roastbeef, roastpork, roastlamb. Please create sensible initial values in these files, although they will be hand modified and validated later.

These should be created and added to the /recipes directory, and should contain the same fields and structure as the roastchicken.json example. They can use the same numeric values, but the ingredient names should be changed as appropriate within the file.

For the prototype, all available valid recipe files in /recipes should appear in the dropdown.

# Calculations

Each recipe step must have either a fixed time, a variable time based on mass, or both. If both are present then the time is the sum of these.
Each of the two time values are optional, they do not have to be present in the step. If neither are present then the step will be displayed without a time, and no time is needed to be added to the total for the whole recipe.

For the variable time, it will be the mass (in kg) selected by the user multiplied by the minutesPerKg value. This should round to the nearest whole minute. Values ending in .5 should round up to the next whole minute.

It is acceptable that some steps may have no total time.

# Styling
The front end page should have some simple styling with a rustic kitchen style, with a wood/cream/brown palette, with warm colours of beige and yellow for backgrounds, and foreground text in a deep chestnut brown.

# User Interface

There should be a single front end page, with a dropdown of recipes available. The user will choose the recipe from the dropdown, and will be presented with a list of ingredients, and a slider for the main ingredient with a text box to the side. The user can adjust the slider from the minimum mass for recipe (inclusive), up to the maximum mass (inclusive), in 0.1kg increments, and the value in the text box will be updated as it slides. The user can also type directly into the text box. The initial default value will be the one provided in the recipe.
The sequence of steps will be displayed, with the name and description of each step, and the temperature and time for that step.
As the user changes the mass, the list of steps will be updated to reflect the recalculated time based on the new mass.

If the typed mass is outside the recipe range, clamp it to the nearest valid value.

In addition to each of the steps, a total time for the recipe will be shown, which is the sum of the calculated times for all the steps.

No button is required to perform the initial calculation, or a recalculation.

The slider should be restricted to the mass range provided in the recipe. It is a required positive number.

If there are no recipes available, the user will be presented with a message "No recipes available".

If any recipe is not valid JSON, it will be ignored. 

# Recipe validation

A command line tool will be created to go through all the recipes and validate them in turn, outputting a nice CLI output with a green "Valid" or red "Invalid" for each recipe.

A recipe is valid if:
- name is present and is a string
- description is present and is a string
- ingredients is present and is an array of strings (must contain at least one ingredient)
- defaultMainIngredientMassKg is a positive number which may have a decimal point
- minimumMass and maximumMass are positive numbers which may have a decimal point
- defaultMainIngredientMassKg is between minimumMass and maximumMass
- steps is an array, which must contain at least one step
- each step has a name and description
- each step may have temperature as a number or null
- each step may optionally have fixedMinutes, minutesPerKg, or both, which if present will be decimal numbers or null, and may be 0 or greater.
- expectedTotalDefaultTimeMinutes is optional, and is a number or null. If it is not present, or is null, then the total time cannot be validated yet.


# Technology

Laravel Sail will be used to create the backend. I will run the initial installation of this.

The front end will be a single Laravel route serving a simple Blade page.

# Prototype

The first version of the prototype will be a single simple web page with a dropdown of recipes, showing all valid recipe files available in /recipes.

It would be ideal for it to work on both a desktop and mobile device (iphone or ipad).