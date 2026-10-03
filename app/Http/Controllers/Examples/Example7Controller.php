<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

/**
 * Example 7. In 2.x "news of the last two days" needed a policy class. In 3.x it is the
 * condition of the permission, see CreateRolesSeeder:
 *
 *     $root->allow('Example7News.update', when: "news.created_at >= ago('48 hours')");
 *
 * The same line answers for one record and filters the list.
 */
class Example7Controller extends Controller
{
    public function index()
    {
        return Response::json(News::allowedTo('Example7News.update')->orderBy('id')->get());
    }

    public function show(News $news)
    {
        Gate::authorize('Example7News.update', $news);

        return Response::json($news->toArray());
    }
}
