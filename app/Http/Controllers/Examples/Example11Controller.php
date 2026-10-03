<?php

namespace App\Http\Controllers\Examples;

use App\Enum\Ability;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

/**
 * Example 11: guests. docs/tutorial-abac-step-by-step.md, example 12.
 *
 * A request without a user is checked as the owner named in config/access.php,
 *     'guest' => ['type' => 'Role', 'id' => 'guest'],
 * and that role holds "catalog.view" with the condition 'not product.restricted'.
 * Open these routes signed out. A signed in user does not get permissions of the guest
 * on top of its own, so Ann and Bob see an empty catalogue until somebody grants them the rule.
 */
class Example11Controller extends Controller
{
    public function index()
    {
        // The ability is a backed enum here, a string works the same
        return Response::json(Product::allowedTo(Ability::CatalogView)->orderBy('id')->get());
    }

    public function show(Product $product)
    {
        Gate::authorize(Ability::CatalogView, $product);

        return Response::json($product);
    }
}
