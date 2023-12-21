<?php

namespace App\Http\Controllers;
use App\Models\Ingredient;
use Illuminate\Http\Request;


class IngredientController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $ingredients = Ingredient::where('name', 'LIKE', "%$query%")->get();
            return response()->json($ingredients);
        } catch (\Exception $e) {
            // Log de l'erreur
            Log::error($e->getMessage());
            // Retourner une réponse d'erreur générique
            return response()->json(['error' => 'Une erreur serveur est survenue'], 500);
        }
    }


    public function addToRecipe(Request $request, $recipeId)
    {
        $validatedData = $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*.ingredientId' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required',
            'ingredients.*.unit' => 'required'
        ]);

        foreach ($validatedData['ingredients'] as $ingredient) {
            RecipeIngredient::create([
                'recipe_id' => $recipeId,
                'ingredient_id' => $ingredient['ingredientId'],
                'quantity' => $ingredient['quantity'],
                'unit' => $ingredient['unit']
            ]);
        }

        return response()->json(['message' => 'Ingredients added successfully']);
    }


}

