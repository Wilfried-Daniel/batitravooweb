<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /** Catégories types écran produits (Matériaux, Équipement) + services. */
    public function run(): void
    {
        $rows = [
            ['name' => 'Matériaux', 'applies_to' => 'product', 'sort_order' => 1],
            ['name' => 'Équipement', 'applies_to' => 'product', 'sort_order' => 2],
            ['name' => 'Services généraux', 'applies_to' => 'service', 'sort_order' => 3],
        ];

        foreach ($rows as $r) {
            $slug = Str::slug($r['name']);
            Category::query()->updateOrCreate(
                ['slug' => $slug],
                $r + ['is_active' => true]
            );
        }
    }
}
