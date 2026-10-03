<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Wnikk\LaravelAccessRules\Facades\Access;

/**
 * Example 12: "why can't I see it?", in the browser. docs/tutorial-abac-step-by-step.md, example 14.
 *
 *     /example12/4                  a plain 403 of Laravel; the page names what was refused
 *     /example12/4?access_debug=1   the same 403 with the cause printed under it
 *     /example12?access_debug=1     a list, and what narrowed it
 *
 * App\Http\Middleware\AccessDebug turns the mode on, resources/views/errors/403.blade.php prints
 * the explanation. The mode observes: the answer is a 403 with the message of Laravel either way.
 */
class Example12Controller extends Controller
{
    public function index()
    {
        $orders = Order::allowedTo('orders.view')->orderBy('id')->pluck('number', 'id');

        return Response::json([
            'orders' => $orders,
            // Empty unless debug mode is on: the conditions that narrowed the list, and the SQL they became
            'lists'  => Access::debugLog()['lists'],
        ]);
    }

    public function show(Order $order)
    {
        Gate::authorize('orders.view', $order);

        return Response::json($order);
    }
}
