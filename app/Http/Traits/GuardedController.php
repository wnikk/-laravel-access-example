<?php

namespace App\Http\Traits;

use App\Http\Controllers\Controller;

trait GuardedController
{
    /**
     * Map of resource methods to ability names
     * @example ['index' => 'viewAny']
     *
     * @var string[]
     */
    //abstract protected $guardedMethods = [];

    /**
     * Do not automatically scan all available methods.
     *
     * @var bool
     */
    //abstract protected $disableAutoScanGuard = true;

    /**
     * List of resource methods which do not have model parameters.
     * @example ['index']
     *
     * @var string[]
     */
    //abstract protected $methodsWithoutModels = ['index'];

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        if (empty($this->disableAutoScanGuard)) {
            $methods = array_diff(
                get_class_methods($this),
                get_class_methods(Controller::class)
            );
            $map = array_combine($methods, $methods);
        } else {
            $map = [];
        }

        $map = array_merge($map, parent::resourceAbilityMap());
        $map = array_merge($map, $this->guardedMethods??[]);

        // Replace name for class App\Http\Controllers\Examples\Example1Controller
        // to guard prefix "Examples.Example1."
        $name = $this->getClassNameGate();

        // Replace standard rule "viewAny" to "Examples.Example1.viewAny"
        foreach ($map as &$item) {$item = $name.$item;}
        unset($item);

        return $map;
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        $base = parent::resourceMethodsWithoutModels();

        return array_merge($base, $this->methodsWithoutModels??[]);
    }

    /**
     * Get name off class witch namespace for guard
     *
     * @param string|null $action
     * @return string
     */
    protected static function getClassNameGate(?string $action = null): string
    {
        // Replace name for class App\Http\Controllers\Examples\Example1Controller
        // to guard prefix "Examples.Example1."
        $name = str_replace([
            'App\\Http\\Controllers\\',
            '\\',
            'Controller'
        ], [
            '', '.', '.'
        ], static::class);

        return $name.$action;
    }
}
