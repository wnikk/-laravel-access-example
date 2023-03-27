<?php
namespace App\Http\Controllers\Examples;

use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\User;

class Example1Controller extends Controller
{
    public function index()
    {
        return Response::json(User::all(), 200);
    }
}
