<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

/**
 * Example 8: one permission with a condition answers for a record and filters the list.
 * docs/tutorial-abac-step-by-step.md, examples 1 and 2.
 *
 *     Access::for('Role', 'manager')->allow('orders.view', when: 'order.client.team_id in user.tenant');
 *
 * No policy class and no hand-written where(): both methods below read that one line.
 */
class Example8Controller extends Controller
{
    /**
     * The list, filtered by the database in the query that loads it. Pagination, sorting
     * and a where() of your own work as always.
     */
    public function index()
    {
        return Response::json(Order::allowedTo('orders.view')->orderBy('id')->paginate());
    }

    /**
     * One record, the usual Laravel way. Every row of index() opens here, and nothing else does.
     */
    public function show(Order $order)
    {
        Gate::authorize('orders.view', $order);

        return Response::json($order->load('client', 'items', 'products'));
    }

    /**
     * A condition that compares the record with the user:
     * 'order.cost <= user.approval_limit && order.user_id != user.id'.
     * The sandbox changes nothing, so the six orders stay as the article lists them.
     */
    public function approve(Order $order)
    {
        Gate::authorize('orders.approve', $order);

        return Response::json(['approved' => $order->number, 'by' => auth()->user()->name]);
    }

    /**
     * An option and a condition of the rule itself together: the manager holds "orders.export"
     * with the option "csv", and the rule says 'not order.locked' for everybody who holds it.
     */
    public function export(Order $order, string $format)
    {
        Gate::authorize('orders.export.'.$format, $order);

        return Response::json(['exported' => $order->number, 'as' => $format]);
    }
}
