<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * The six orders of docs/tutorial-abac-step-by-step.md, Step 6. The data is small on
     * purpose: every result of /example9 can be checked by eye against the table of the article.
     *
     * Dates are relative to the moment of seeding, as the article words them: "today 08:00",
     * "10 days ago". Seed again when "7 days ago" has moved past them.
     */
    public function run(): void
    {
        foreach ([[1, 'Acme', 'Berlin', 1], [2, 'Bolt', 'Paris', 1], [3, 'Core', 'Berlin', 2], [4, 'Dune', null, 2]] as [$id, $name, $city, $team]) {
            Client::create(['id' => $id, 'name' => $name, 'city' => $city, 'team_id' => $team]);
        }

        // Electronics -> Phones, Laptops; Furniture stands alone
        foreach ([[1, null, 'Electronics'], [2, 1, 'Phones'], [3, 1, 'Laptops'], [4, null, 'Furniture']] as [$id, $parent, $name]) {
            Category::create(['id' => $id, 'parent_id' => $parent, 'name' => $name]);
        }

        foreach ([[1, 'Phone', false], [2, 'Charger', false], [3, 'Battery pack', true]] as [$id, $name, $restricted]) {
            Product::create(['id' => $id, 'name' => $name, 'restricted' => $restricted]);
        }

        $today = now()->startOfDay();

        $orders = [
            // id, number, client, author, dept, category, cost, budget, status, locked, created, prices of items, product => quantity
            [1, 'EU-1001', 1, 1, 1, 2, 150, 200, 'draft', false, $today->copy()->setTime(8, 0), [100, 50], [1 => 1, 2 => 2]],
            [2, 'EU-1002', 2, 1, 1, 3, 900, 800, 'review', false, now()->subDays(10), [300, 300, 200, 100], [3 => 5]],
            [3, 'US-2001', 3, 2, 2, 4, 50, 100, 'paid', false, $today->copy()->setTime(11, 0), [50], []],
            [4, 'EU-1003', 4, 2, 1, 1, 450, 600, 'draft', true, now()->subDay(), [450], [1 => 1, 3 => 1]],
            [5, 'US-2002', 1, 2, 2, 2, 120, null, 'draft', false, now()->subDays(20), [], []],
            [6, 'EU-1004', null, 1, 2, 4, 700, 700, 'review', false, $today->copy()->setTime(0, 30), [400, 200, 100], [2 => 10]],
        ];

        foreach ($orders as [$id, $number, $client, $author, $department, $category, $cost, $budget, $status, $locked, $created, $prices, $products]) {
            $order = Order::create([
                'id' => $id, 'number' => $number, 'client_id' => $client, 'user_id' => $author,
                'department_id' => $department, 'category_id' => $category, 'cost' => $cost, 'budget' => $budget,
                'status' => $status, 'locked' => $locked, 'created_at' => $created,
            ]);

            foreach ($prices as $price) {
                $order->items()->create(['price' => $price]);
            }

            foreach ($products as $product => $quantity) {
                $order->products()->attach($product, ['quantity' => $quantity]);
            }
        }
    }
}
