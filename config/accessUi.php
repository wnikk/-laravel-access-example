<?php

return [
    /**
     * Panel Register
     *
     * This manages if routes used for the admin panel should be registered.
     * Turn this value to false if you don't want to use admin panel
     */
    'register' => true,

    /**
     * Panel Domain
     *
     * This is the Domain Laratrust panel for roles and permissions
     * will be accessible from.
     */
    'domain' => env('ACCESSUI_PANEL_DOMAIN', (app()->runningInConsole() === false) ? request()->getHost() : 'localhost'),

    /**
     * Panel Path
     *
     * This is the URI path where Laratrust panel for roles and permissions
     * will be accessible from.
     */
    'path' => 'accessui',

    /**
     * Panel Route Middleware
     *
     * These middleware will get attached onto each Laratrust panel route.
     */
    'middleware' => ['web'],

    /**
     * Enable permissions assignment
     *
     * Enable/Disable the permissions assignment to the users.
     */
    'assign_permissions_to_user' => true,

    /**
     * Enable permissions creation
     *
     * Enable/Disable the possibility to create/update/delete rules from the panel.
     */
    'grid_rules' => true,

    /**
     * Enable view owners
     *
     * Enable/Disable the possibility to create/update/delete owners from the panel.
     */
    'grid_owners' => true,

    /**
     * Enable view inheritance
     *
     * Enable/Disable the possibility to add/remove inheritance from the panel.
     */
    'grid_inherit' => true,

    /**
     * Enable view permission
     *
     * Enable/Disable the possibility to add/remove permission from the panel.
     */
    'grid_permission' => true,
];
