<?php
namespace App\Http\Controllers\Examples;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\News;

class Example6Controller extends Controller
{
    public function update(Request $request, News $news)
    {
        Gate::authorize('example6.update', $news);

        $news->fill($request->toArray());

        return Response::json($news->save?1:0);
    }
}
