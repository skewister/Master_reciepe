<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Http;

class ImportIngredients extends Command
{
    protected $signature = 'import:themealdb-ingredients';
    protected $description = 'Import ingredients from TheMealDB API';

    public function handle()
    {
        $apiUrl = 'https://www.themealdb.com/api/json/v1/1/list.php?i=list';

        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $data = $response->json();

            foreach ($data['meals'] as $meal) {
                $name = $meal['strIngredient'];
                $id = $meal['idIngredient']; // ID fourni par TheMealDB
                $picture = "https://www.themealdb.com/images/ingredients/$name.png"; // Construction de l'URL de l'image

                // Enregistrez l'ingrédient dans la base de données
                Ingredient::create([
                    'id' => $id,
                    'name' => $name,
                    'picture' => $picture
                ]);

                $this->info("ID: $id, Name: $name, Picture: $picture");
            }
        } else {
            $this->error('Erreur lors de la requête à l\'API TheMealDB.');
        }

        $this->info('Importation des ingrédients de TheMealDB terminée.');
    }
}
