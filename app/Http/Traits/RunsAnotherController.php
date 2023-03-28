<?php

namespace App\Http\Traits;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\MiddlewareNameResolver;
use Illuminate\Routing\SortedMiddleware;

trait RunsAnotherController
{
    /**
     * @param object $controller
     * @param string $method
     * @return mixed
     */
    public function runController($controller, $method = 'index')
    {
        $middleware = $this->gatherControllerMiddleware($controller, $method);

        $middleware = $this->sortMiddleware($middleware);

        return $response = (new Pipeline(app()))
            ->send(request())
            ->through($middleware)
            ->then(function ($request) use ($controller, $method) {
                return app('router')->prepareResponse(
                    $request,
                    (new ControllerDispatcher(app()))->dispatch(
                        app('router')->current(),
                        $controller,
                        $method
                    )
                );
            });
    }

    /**
     * @param $controller
     * @param $method
     * @return \Illuminate\Support\Collection
     */
    protected function gatherControllerMiddleware($controller, $method)
    {
        return collect($this->controllerMiddleware($controller, $method))->map(function ($name) {
            return (array)MiddlewareNameResolver::resolve(
                $name,
                app('router')->getMiddleware(),
                app('router')->getMiddlewareGroups()
            );
        })->flatten();
    }

    /**
     * @param $controller
     * @param $method
     * @return array
     */
    protected function controllerMiddleware($controller, $method)
    {
        return (new ControllerDispatcher(app()))->getMiddleware(
            $controller, $method
        );
    }

    /**
     * @param $middleware
     * @return array
     */
    protected function sortMiddleware($middleware)
    {
        return (new SortedMiddleware(app('router')->middlewarePriority, $middleware))->all();
    }
}
