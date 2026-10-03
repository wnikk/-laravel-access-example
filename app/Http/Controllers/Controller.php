<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Laravel 11 removed the base controller's parent class and its traits. The examples need
     * both back: `authorize()` and `authorizeResource()` come from the trait, and
     * `authorizeResource()` registers its checks through `middleware()` of the routing controller.
     */
    use AuthorizesRequests;
}
