<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\Order;

/**
 * Example 10: three ways to ask, and a menu. docs/tutorial-abac-step-by-step.md, example 6.
 *
 *     $user->can('orders.view', $order);         this very order
 *     $user->can('orders.view', Order::class);   orders in general: is there any order I may see?
 *     $user->can('orders.view');                 nothing to look at: only permissions without a condition count
 *
 * A menu item has no record yet, so it asks with the class. The third form stays strict on
 * purpose: a forgotten argument must not open every order to anyone who may see one.
 */
class Example10Controller extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $order = Order::find(1);

        return view('examples.menu', [
            'answers' => [
                "can('orders.view', \$order)  // order 1"  => $user?->can('orders.view', $order),
                "can('orders.view', Order::class)"         => $user?->can('orders.view', Order::class),
                "can('orders.view')"                       => $user?->can('orders.view'),
            ],
        ]);
    }
}
