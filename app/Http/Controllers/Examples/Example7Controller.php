<?php

namespace App\Http\Controllers\Examples;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\News;

class Example7Controller extends Controller
{

    public function index(News $news)
    {
        $this->authorize('viewThisIfThisIsTest', $news);

        return Response::json($news->toArray());
    }
}
