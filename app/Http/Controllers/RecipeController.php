<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Tag;
use App\Models\TagType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

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
         $request->validate([
            'title' => 'required|unique:recipes',
            'description' => 'required|string|max:1000',
            'time_to_cook_tag_id' => 'required|exists:tags,id',
            'time_to_prep_tag_id' => 'required|exists:tags,id',
            'difficulty_tag_id' => 'required|exists:tags,id',
            'saison_tag_id' => 'exists:tags,id',
            'type_plat_id' => 'exists:tags,id',
            'type_cuisine_id' => 'exists:tags,id',
            'nutriment_id' => 'exists:tags,id',
            'methode_cuisson_id' => 'exists:tags,id',
            'image' => 'required|unique:recipes',
            'video' => 'unique:recipes',
        ]);

        $allTags = [];

        $tagAssociations = [
            'nutriment_id' => 8,
            'Methode_cuisson_id' => 8,
            'type_cuisine_id' => 7,
            'type_plat_id' => 1,
            'saison_tag_id' => 6,
            'time_to_cook_tag_id' => 4,
            'time_to_prep_tag_id' => 5,
            'difficulty_tag_id' => 3
        ];

        foreach ($tagAssociations as $requestKey => $tagTypeId) {
            if ($request->has($requestKey)) {
                Log::debug($tagTypeId);
                $tag = TagType::where('id', $tagTypeId)->first()->tags()->where('id', $request->$requestKey)->first();
                if (!$tag) {
                    return response()->json([
                        'message' => "l'ID renseigné n'est pas de la bonne catégorie"
                    ], 400);
                }
                $allTags[] = $request->$requestKey;
            }
        }

        $request->user_id = auth()->user()->id;

        $recipe = Recipe::create($request->all());

        $recipe->tags()->attach($allTags);

        return response()->json([
            'message' => 'Recipe created successfully.',
            'data' => $recipe
        ], 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        return response()->json($recipe);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required|unique:recipes,name,' . $recipe->id,
        ]);

        $recipe->update($request->all());

        return response()->json([
            'message' => 'Recipe updated successfully',
            'data' => $recipe
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
