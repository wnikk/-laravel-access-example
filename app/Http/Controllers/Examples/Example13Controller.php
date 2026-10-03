<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Wnikk\LaravelAccessRules\Administration\Linter;

/**
 * Example 13: what an admin panel builds its "why?" and "health" screens from.
 * docs/tutorial-abac-step-by-step.md, examples 14-16. The routes need "can:manage-access":
 * an explanation shows rules of other owners.
 *
 * The console gives the same answers:
 *     php artisan acr:explain "App\Models\User" 1 orders.view order:4
 *     php artisan acr:lint
 */
class Example13Controller extends Controller
{
    /**
     * Every permission that takes part, strongest first, the one that decided, the values
     * its condition read, and whether the cache agrees with the database.
     */
    public function explain(User $user, string $ability, ?Order $order = null)
    {
        return Response::json($user->access()->explain($ability, $order ?? Order::class));
    }

    /**
     * Stored conditions, rules and owners against models and config as they are now.
     * Findings come as data: a code for an icon, a table and a key to link to.
     */
    public function lint(Linter $linter)
    {
        return Response::json($linter->run());
    }

    /**
     * The audit log that App\Providers\AppServiceProvider writes from the event AccessChanged.
     */
    public function audit()
    {
        $file = storage_path('logs/access-audit.log');

        return Response::json(is_file($file) ? array_slice(file($file, FILE_IGNORE_NEW_LINES), -30) : []);
    }
}
