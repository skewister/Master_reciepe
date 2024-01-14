<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRequest;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\Tag;
use App\Models\TagType;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recipes = Recipe::with('tags.tagType')->get();

        $recipes->each(function($recipe){
            if($recipe->image && $recipe->image !==""|| $recipe->image !==('(Null)')){
                $recipe->image_url= asset(Storage::url($recipe->image));
            }
        });
        return response()->json([
            'message' => 'Recettes récupérées avec succès',
            'data' => $recipes
        ]);
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
            'ingredients' => 'required|array',
            'ingredients.*.ingredientId' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required',
            'ingredients.*.unit' => 'required',
            'image'=> 'nullable|image|mimes:jpeg,png,jpg'
        ]);

        //Gestion image

        try{
            $imagePath = null;
            if($request -> hasFile('image')){
                if($request->file('image')->isValid()){
                    $imageName = $request->file('image')->getClientOriginalName();
                    $imagePath = $request->file('image')->storeAs('recipe_image',$imageName,'public',);
                }else{
                    throw new \Exception('Fichier image invalide');
                }

            }
            // Création de la recette
            $recipeData = $request->only(['title', 'description', 'image', 'video']);
            $recipeData['user_id'] = auth()->id();
            $recipeData['image'] = $imagePath;
            $recipe = Recipe::create($recipeData);

            // Association des tags
            $recipe->tags()->attach($request->input('tags'));
            $recipe->tags()->attach($request->input('prep_time'));
            $recipe->tags()->attach($request->input('cook_time'));
            $recipe->tags()->attach($request->input('difficulty'));

            // Traitement des ingrédients
            foreach ($request->input('ingredients') as $ingredient) {
                $recipe->ingredients()->attach($ingredient['ingredientId'], [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit']
                ]);
            }

            // Réponse en cas de succès
            return response()->json([
                'message' => 'Recette parfaitement créé.',
                'data' => $recipe->load('ingredients') // Charger les ingrédients avec la recette
            ], 201);
        }catch (Exception $e){
            return response($e);
        }



    }



    public function getStepsByRecipe(Recipe $recipe)
    {
        // Assuming Recipe has many Steps
        return response()->json($recipe->steps);
    }


    public function getIngredientsByRecipe($recipeId)
    {

        $recipe = Recipe::with('ingredients')->findOrFail($recipeId);
        return response()->json($recipe->ingredients);
    }


    public function getTagsByRecipe($recipeId)
    {
        $recipe = Recipe::with(['tags.tagType'])->findOrFail($recipeId);
        return response()->json($recipe->tags);
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
    public function update(UpdateRequest $request, Recipe $recipe)
    {
        // Valider la requête
        try{

            $recipe->title=$request->title;
            $recipe->description = $request->description;

            // Vérifie si l'utilisateur qui est lié à la recette est le même que celui qui veut faire l'edition
            if($recipe->user_id == auth()->user()->id) {

                //Verifie si on ajoute/modifie des ingrédients
                $isset = $request->input(("ingredients"));

                if(isset($isset)){
                    $recipe->ingredients()->detach();
                    // Ajouter les nouveaux ingrédients

                    foreach ($request->input('ingredients') as $ingredient) {
                        $recipe->ingredients()->attach($ingredient['ingredientId'], [
                            'quantity' => $ingredient['quantity'],
                            'unit' => $ingredient['unit']
                        ]);
                    }
                }
                $issetTag= $request-> input(("tags"));
                if(isset($issetTag)){
                    $recipe->tags()->detach();
                    $recipe->tags()->attach($request->input('tags'));
                    $recipe->tags()->attach($request->input('prep_time'));
                    $recipe->tags()->attach($request->input('cook_time'));
                    $recipe->tags()->attach($request->input('difficulty'));
                }

                    $recipe->save();
                    // Réponse en cas de succès
                    return response()->json([
                        'message' => 'Recipe updated successfully.',
                        'data' => $recipe->load('ingredients', 'tags') // Charger les ingrédients et les tags avec la recette
                    ]);

            }
            }catch(Exception $e){
            return response()->json($e);
        }

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
        // Validation des données de la requête
        $request->validate([
            'description' => 'required|string|max:1000',
            'step_number' => 'required|integer',
            'video' => 'nullable|file|mimes:mp4,avi,mov', // Validez les types de fichiers vidéo
        ]);

        // Gestion du stockage de la vidéo
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('steps_videos', 'public');
        }


        // Créer la nouvelle étape avec le chemin de la vidéo
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
            'video' => 'nullable|file|mimes:mp4,avi,mov', // Validez les types de fichiers vidéo
        ]);

        // Gérer la mise à jour ou la suppression de la vidéo
        if ($request->hasFile('video')) {
            // Si une nouvelle vidéo est fournie, stockez-la et mettez à jour le chemin
            $videoPath = $request->file('video')->store('steps_videos', 'public');
            $step->video = $videoPath;
        } elseif ($request->input('remove_video') === true) {
            // Si la requête indique de supprimer la vidéo, faites-le
            Storage::delete($step->video);
            $step->video = null;
        }

        // Mettre à jour les autres attributs de l'étape
        $step->description = $request->input('description');
        $step->step_number = $request->input('step_number');
        $step->save();

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
