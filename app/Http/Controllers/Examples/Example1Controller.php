<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Response;

/**
 * Example 1: the check lives in the route, ->middleware('can:example1.viewAny').
 */
class Example1Controller extends Controller
{
    public function index()
    {
        return Response::json(User::all(), 200);
    }
}
