<?php
namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Http\Traits\GuardedController;
use App\Http\Traits\RunsAnotherController;
use Wnikk\LaravelAccessRules\Contracts\AccessRules as AccessRulesContract;
use Wnikk\LaravelAccessUi\Http\Controllers\RulesController;
use Wnikk\LaravelAccessUi\Http\Controllers\OwnersController;
use Wnikk\LaravelAccessUi\Http\Controllers\InheritController;
use Wnikk\LaravelAccessUi\Http\Controllers\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UserRulesController extends Controller
{
    use GuardedController, RunsAnotherController;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource('roles');
    }


    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function main()
    {
        return view('user-rules');
    }

    /**
     * Call RulesController
     *
     * @param $action
     * @return array
     */
    public function rules($action, RulesController $rules)
    {
        if (!in_array($action, ['index', 'store', 'update', 'destroy'])) abort(404);

        Gate::authorize($this->getClassNameGate(__FUNCTION__.'.'.$action));

        return $this->runController($rules, $action);
    }

    /**
     * Call OwnersController
     *
     * @param $action
     * @return array
     */
    public function roles($action, OwnersController $ownersController, AccessRulesContract $acr)
    {
        if (!in_array($action, ['index', 'store', 'update', 'destroy'])) abort(404);

        Gate::authorize($this->getClassNameGate(__FUNCTION__.'.'.$action));

        $types = $acr->getListTypes();
        $types = [
            array_search('Role', $types) => 'Role',
        ];

        $ownersController->setSupportTypes($types);

        return $this->runController($ownersController, $action);
    }

    /**
     * Call InheritController
     *
     * @param $action
     * @return array
     */
    public function inherit($action, InheritController $rules)
    {
        if (!in_array($action, ['index', 'store', 'destroy'])) abort(404);

        Gate::authorize($this->getClassNameGate(__FUNCTION__.'.'.$action));

        return $this->runController($rules, $action);
    }

    /**
     * Call InheritController
     *
     * @param $action
     * @return array
     */
    public function permission($action, PermissionController $rules)
    {
        if (!in_array($action, ['index', 'update'])) abort(404);

        Gate::authorize($this->getClassNameGate(__FUNCTION__.'.'.$action));

        return $this->runController($rules, $action);
    }
}
