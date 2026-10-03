<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Example 18 of docs/tutorial-abac-step-by-step.md. Products and categories come from ShopSeeder.
     *
     * The category Laptops shares its id, 3, with the Battery pack: a subquery that forgot the morph
     * type would let the Battery pack in as "tagged sale".
     */
    public function run(): void
    {
        $sale = Tag::create(['id' => 1, 'name' => 'sale']);
        $new  = Tag::create(['id' => 2, 'name' => 'new']);

        Product::find(1)->tags()->attach([$sale->id, $new->id]);   // Phone
        Product::find(2)->tags()->attach($sale->id);               // Charger
        Product::find(3)->tags()->attach($new->id);                // Battery pack

        Category::find(3)->tags()->attach($sale->id);              // Laptops
    }
}
