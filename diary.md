Here's a sequence of what I did in creating this project.

AI : Ignore this file! It's only a list of my sequence and thoughts for reference.


# Start 
Created a project directory, with a readme.md file, and (as suggested) an initial spec.md file.
The spec was initially very rough with just a desciption of the project, including a few details
* Overview
* Use Laravel Sail
* Expectations
* Styling

# Using JetBrains AI

I asked JetBrains AI to evaluate the spec.md file, and it gave me useful suggestions of what to improve, listing a number of areas that I'd completely missed out.

I went through a number of rounds of adding to the specifications with details, including an example JSON for a recipe, etc. 

After each addition I asked JetBrains AI to evaluate the spec.md file again, and it gave further specifics and guidance.

At each stage the AI was useful in terms of saying how helpful it was towards an MVP, any uncertainties, suggestions for improvements, and spotted various things like inconsistencies (g versus kg), and spelling errors to fix.

Especially helpful was that it evaluated my example recipe JSON and gave me a replacement format in the right shape, fixing some of my errors (and telling me what they were.)

Finally it was able to say that it was sufficiently complete for making the project MVP.

# How to continue

"OK. What are my steps from here?"


Your best next step is to move from **spec finalisation** into **project setup and staged implementation**.
## 1. Make the final small spec fixes
Before coding, tidy the remaining contradictions so you do not have to decide them mid-build.
Update the spec to clarify:
- Use **kilograms**, not grams, throughout the UI.
- The prototype should either:
    - show **all valid recipes** in `/recipes`, or
    - show **only roast chicken** initially.

I recommend:
The prototype should show all valid recipe files in /recipes.
Also fix minor wording:
- `celcius` → `celsius`
- `mininum` → `minimum`
- `ingredients is present is an array` → `ingredients is present and is an array`

## 2. Create the Laravel project
Since your spec says you will run the initial installation with Laravel Sail, do that next.
Typical flow:``` bash
curl -s "https://laravel.build/recipe" | bash
cd recipe
./vendor/bin/sail up -d
```

Then check the app loads in the browser.
3. Commit the clean starting point
Before adding app code, make an initial commit.``` bash
git init
git add .
git commit -m "Initial Laravel Sail project"
```

This gives you a clean rollback point.
4. Add the /recipes directory
   Create a top-level recipe data directory:``` text
   recipes/
   roastchicken.json
   roastbeef.json
   roastpork.json
   roastlamb.json
```

Start with roastchicken.json exactly matching the validated example from your spec.
For the other recipe files, use the same structure and sensible placeholder values, as your spec allows.
5. Build recipe loading on the backend
Create a small service or helper that:
scans /recipes for .json files,
reads each file,
decodes JSON,
ignores invalid JSON,
returns valid recipe data to the page.
At this stage, only worry about loading valid JSON. Full validation can come next.
6. Add recipe validation rules
Create validation logic matching your spec.
A recipe should be considered valid if it has:
name
description
ingredients
defaultMainIngredientMassKg
minimumMass
maximumMass
steps
And optional:
expectedTotalDefaultTimeMinutes
Also validate step fields:
each step has name
each step has description
temperature may be number or null
fixedMinutes may be number, null, or absent
minutesPerKg may be number, null, or absent
This validation should be reused by both:
the web page loader,
the CLI validation command.
7. Create the command-line recipe validator
Add an Artisan command, for example:``` bash
php artisan recipes:validate
```

It should:
scan all recipe JSON files,
show each filename,
mark valid recipes as green Valid,
mark invalid recipes as red Invalid,
ideally show the reason a file is invalid.
This is a useful early step because it helps you catch recipe-file mistakes before working on the UI.
8. Create the single Laravel route
   Add one route for the app homepage.
   For example:``` text
   /
```

The route should load the valid recipes and pass them into a Blade view.
If no recipes are available, the page should display:``` text
No recipes available
```

9. Build the Blade page
   Create a simple page with:
   title/header,
   recipe dropdown,
   ingredients list,
   mass slider,
   mass text input,
   steps list,
   total time display.
   Keep it simple first. Do not over-style it yet.
   The first goal is functional correctness.
10. Add JavaScript calculation
    On the page, add JavaScript that:
    stores the loaded recipe data,
    responds to recipe dropdown changes,
    sets the default mass,
    sets slider min/max from the selected recipe,
    keeps slider and text input in sync,
    clamps typed values to the recipe min/max,
    recalculates each step time live,
    recalculates the total time live.
    The calculation should follow your spec:``` text
    variable time = round(selectedMassKg × minutesPerKg)
    step time = fixedMinutes + variable time
```

With .5 rounded up.
11. Display the step output clearly
For each step, show:
step name,
step description,
temperature, if not null,
calculated time, if the step has a time.
For steps with no time, display something like:``` text
No timed duration
```

For steps with temperature: null, display something like:``` text
No oven temperature
```

12. Add rustic styling
Once the page works, add the visual style:
cream/beige background,
warm yellow panels,
chestnut brown text,
rustic card layout,
readable spacing,
mobile-friendly width,
slider/input layout that works on phone and tablet.
Keep styling in a CSS file or in the Blade page for the prototype, depending on how tidy you want the first version to be.
13. Test manually
Use roast chicken first.
Check that the default mass gives:``` text
92 minutes
```

Then test:
changing slider updates step times,
typing in the box updates slider and results,
below-minimum value clamps,
above-maximum value clamps,
recipe dropdown changes the displayed recipe,
invalid JSON recipe is ignored,
no recipes message works if the directory is empty or all files are invalid.
14. Add automated tests if you want confidence
    Useful tests would be:
    recipe loader ignores invalid JSON,
    validator accepts valid recipe,
    validator rejects missing required fields,
    total expected time matches expectedTotalDefaultTimeMinutes,
    calculation rounds .5 up.
    For a small prototype, tests are optional, but the calculation and validation logic are good candidates.
    Suggested implementation order
    I would do it in this order:
    Final spec cleanup.
    Laravel Sail install.
    Add recipe JSON files.
    Build recipe loader.
    Build recipe validator.
    Create homepage route and Blade page.
    Pass recipes to JavaScript.
    Implement live calculations.
    Add styling.
    Manual test.
    Commit working prototype.
    Suggested first milestone
    Aim for this first:
    A Laravel page at / that loads roastchicken.json, lets you change mass, recalculates the step times live, and shows the total time as 92 minutes at the default mass.
    Once that works, adding the other recipes and polish will be straightforward.