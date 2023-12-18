<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Step;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recipes = Recipe::all();
        return response()->json($recipes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validation des données de la requête
        $request->validate([
            'title' => 'required',
            'description' => 'required|string|max:1000',
            'prep_time' => 'required|exists:tags,id',
            'cook_time' => 'required|exists:tags,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'difficulty' => 'required|exists:tags,id',
        ]);

        // Création de la recette
        $recipeData = $request->only(['title', 'description', 'image', 'video']);
        $recipeData['user_id'] = auth()->id();
        $recipe = Recipe::create($recipeData);

        // Association des tags
        $recipe->tags()->attach($request->input('tags'));
        $recipe->tags()->attach($request->input('prep_time'));
        $recipe->tags()->attach($request->input('cook_time'));
        $recipe->tags()->attach($request->input('difficulty'));

        // Réponse en cas de succès
        return response()->json([
            'message' => 'Recipe created successfully.',
            'data' => $recipe
        ], 201);
    }




    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        $recipe = Recipe::with(['tags' => function ($query) {
            //les types de tags à inclure
            $query->whereIn('tag_type_id', [3, 4, 5]);
        }])->findOrFail($id);

        // informations supplémentaires sur les tags
        $recipe->tags->each(function ($tag) {
            $tag->additional_info = '...';
        });

        return response()->json($recipe);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        // Valider la requête
        $request->validate([
            'title' => 'required',
            'description' => 'required|string|max:1000',
            'prep_time' => 'required|exists:tags,id',
            'cook_time' => 'required|exists:tags,id',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
            'difficulty' => 'required|exists:tags,id',
        ]);

        // Mettre à jour les données de la recette
        $recipeData = $request->only(['title', 'description', 'image', 'video']);
        $recipe->update($recipeData);


        // Mettre à jour les tags si le tableau de tags est fourni
        if ($request->has('tags')) {
            // Mettre à jour les tags associés à la recette
            $recipe->tags()->sync($request->tags);
        }

        // Associer les tags de 'prep_time' et 'cook_time' s'ils sont fournis
        if ($request->has('prep_time')) {
            $recipe->tags()->syncWithoutDetaching($request->input('prep_time'));
        }
        if ($request->has('cook_time')) {
            $recipe->tags()->syncWithoutDetaching($request->input('cook_time'));
        }
        if ($request->has('difficulty')) {
            $recipe->tags()->syncWithoutDetaching($request->input('difficulty'));
        }

        // Réponse en cas de succès
        return response()->json([
            'message' => 'Recipe updated successfully.',
            'data' => $recipe
        ]);
    }



    /**
     * Search in fonction of title and tag and ingredients.
     */
    public function search(Request $request)
    {
        $query = Recipe::query();

        // Recherche par titre de recette
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        // Recherche par ingrédients (ajustez en fonction de votre structure de base de données)
        if ($request->has('ingredient')) {
            // Supposons que vous ayez une relation 'ingredients' dans votre modèle Recipe
            $query->whereHas('ingredients', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('ingredient') . '%');
            });
        }

        // Recherche par tags
        if ($request->has('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('id', $tags);
            });
        }

        $recipes = $query->get();
        return response()->json($recipes);
    }


    /**
     * add step to recipe.
     */
public function addStep(Request $request, Recipe $recipe)
{
    $request->validate([
        'description' => 'required|string|max:1000',
        'step_number' => 'required|integer',
        'video' => 'nullable|file', // Validation pour le fichier vidéo
    ]);

    $videoPath = null;
    if ($request->hasFile('video')) {
        $videoPath = $request->file('video')->store('steps_videos', 'public');
    }

    $step = new Step([
        'recipe_id' => $recipe->id,
        'description' => $request->description,
        'step_number' => $request->step_number,
        'video' => $videoPath,
    ]);

    $recipe->steps()->save($step);

    return response()->json([
        'message' => 'Step added successfully.',
        'data' => $step
    ], 201);
}


    public function listSteps(Recipe $recipe)
    {
        return response()->json($recipe->steps);
    }


    public function updateStep(Request $request, Recipe $recipe, Step $step)
    {
        $request->validate([
            'description' => 'sometimes|required|string|max:1000',
            'step_number' => 'sometimes|required|integer',
        ]);

        $step->update($request->all());

        return response()->json([
            'message' => 'Step updated successfully',
            'data' => $step
        ]);
    }


    public function deleteStep(Recipe $recipe, Step $step)
    {
        $step->delete();

        return response()->json([
            'message' => 'Step deleted successfully'
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->json([
            'message' => 'Recipe deleted successfully'
        ]);
    }
}
