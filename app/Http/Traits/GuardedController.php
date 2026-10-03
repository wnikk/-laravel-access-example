<?php

namespace App\Http\Traits;

/**
 * Example 5: abilities of authorizeResource() get the name of the controller in front,
 * "viewAny" becomes "Examples.Example5.viewAny", so every controller has rules of its own.
 */
trait GuardedController
{
    /**
     * @return array<string, string>
     */
    protected function resourceAbilityMap()
    {
        $prefix = static::guardPrefix();

        return array_map(fn (string $ability): string => $prefix.$ability, parent::resourceAbilityMap());
    }

    /**
     * App\Http\Controllers\Examples\Example5Controller gives "Examples.Example5."
     */
    protected static function guardPrefix(): string
    {
        $name = substr(static::class, strlen('App\\Http\\Controllers\\'));
        $name = preg_replace('/Controller$/', '', $name);

        return str_replace('\\', '.', $name).'.';
    }
}
