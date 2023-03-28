<?php

namespace App\Http\Controllers\Examples;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Models\News;

class Example4Controller extends Controller
{
    /**
     * Create the controller instance.
     *
     */
    public function __construct()
    {
        $this->authorizeResource(News::class, 'News');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Response::json(News::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $news = News::create($request->toArray());

        return Response::json($news->id, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return Response::json($news->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $news->fill($request->toArray());

        return Response::json($news->save);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        return Response::json($news->delete());
    }
}
