<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Nothing is registered until BOTH `prefix` and `middleware` are filled in.
    |
    | That is deliberate. These screens hand out permissions, so a route group
    | that is reachable without a guard is an open door to everything else in
    | the application. An unconfigured install therefore exposes no endpoints
    | at all rather than exposing unguarded ones.
    |
    | `middleware` must name whatever your application uses to mark an
    | administrator. Any of these works:
    |
    |     ['web', 'auth', 'can:manage-access']   // a Gate
    |     ['web', 'auth', 'can:system.access']   // a rule from access-rules itself
    |     ['web', 'auth', 'admin']               // your own middleware
    |
    | `['web', 'auth']` alone is not enough: it lets every signed-in account
    | grant itself every permission in the system.
    |
    */
    'routes' => [

        // URL prefix for every route below, e.g. 'access-control'. Null = off.
        'prefix' => env('ACCESS_UI_PREFIX'),

        // Middleware stack applied to the whole group. Empty = off.
        // Filled in for this sandbox. ['web', 'auth'] alone is NOT enough: the manage-access
        // ability is defined in App\Providers\AppServiceProvider.
        'middleware' => ['web', 'auth', 'can:manage-access'],

        // Optional host restriction; null means "any host that reaches Laravel".
        'domain' => env('ACCESS_UI_DOMAIN'),

        // Route-name prefix. Change only if it collides with your own names.
        'as' => 'accessUi.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Name a Blade layout and the panel renders inside it, so the section is
    | part of your admin area from the first request. Leave it null and the
    | panel serves its own standalone page instead: enough to work with, and
    | not something to keep once the section has a home.
    |
    | The layout has to yield the named section and nothing else; the panel
    | brings its own CSS and JS (see `assets` below).
    |
    */
    'layout' => [

        // e.g. 'layouts.admin'. Null = built-in standalone page.
        'view' => 'layouts.admin',

        // Section the panel content is pushed into.
        'section' => 'content',

        // Page title, used by the standalone page and passed to the layout.
        'title' => 'Access control',
    ],

    /*
    |--------------------------------------------------------------------------
    | Appearance
    |--------------------------------------------------------------------------
    |
    | 'auto'  follow the operating system, via prefers-color-scheme
    | 'light' always light
    | 'dark'  always dark
    |
    | Set either of the last two and the mount point carries a `wacu-light` or
    | `wacu-dark` class. Those classes work on any ancestor too, so a host that
    | already toggles its own theme by class can put one on <html> and leave
    | this at 'auto'.
    |
    */
    'theme' => 'auto',

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | The whole interface is one JS file and one CSS file, published with:
    |
    |     php artisan vendor:publish --tag=accessUi-assets
    |
    | Both are self-contained: Vue and every other dependency is bundled, so
    | the panel works on a machine with no internet access.
    |
    */
    'assets' => [

        // Public URL the published files are served from.
        'base' => '/vendor/accessui',

        // Emit <link> and <script> tags on the panel page. Turn this off if
        // you bundle the two files through your own asset pipeline instead.
        'inject' => true,

        // Appended to the asset URLs to bust caches after an upgrade.
        'version' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Entities
    |--------------------------------------------------------------------------
    |
    | An "owner" is anything that can hold permissions. access-rules keeps them
    | all in one table, told apart by a numeric type derived from a label.
    |
    | This panel never touches your models. It reads and writes the owner table
    | and nothing else, which is what lets it be dropped into an application it
    | knows nothing about. So an entity here is just a label, a name to show,
    | and two decisions about it.
    |
    | Per entity:
    |
    |   type        Label passed to access-rules. Must also be listed in
    |               config/access.php under `owner_types`. Null means "whatever
    |               this application calls a user", read from
    |               auth.providers.users.model; the class name is used as a
    |               string, the class itself is never loaded.
    |   label       Plural name shown in the interface.
    |   single      Singular name shown in the interface.
    |   create      May the panel add owner rows of this type by hand? True for
    |               roles and groups, which exist only because somebody made
    |               them here. False for anything the application creates on its
    |               own: a user's owner row appears the first time the
    |               application touches them, and typing an id by hand would
    |               only invite a typo that holds permissions.
    |   assignable  May it be handed out as a source of rights? These are what
    |               the assignment widget offers on a user's page. Roles and
    |               groups yes; users normally no, even though access-rules
    |               would allow one user to inherit from another.
    |
    | Two things happen regardless of what is listed here:
    |
    |   - any owner holding a permission or a prohibition is shown on the owners
    |     and inheritance screens, whatever its type. A grant made straight to
    |     one account is otherwise invisible on a screen listing only roles, and
    |     an invisible grant is the kind nobody remembers making;
    |   - any owner in the table may be added as an inheritor on the inheritance
    |     screen. Who may receive rights is not a decision this list makes.
    |
    */
    'entities' => [

        'role' => [
            'type'       => 'Role',
            'label'      => 'Roles',
            'single'     => 'Role',
            'create'     => true,
            'assignable' => true,
        ],

        // 'group' => [
        //     'type'       => 'Group',
        //     'label'      => 'Groups',
        //     'single'     => 'Group',
        //     'create'     => true,
        //     'assignable' => true,
        // ],

        'user' => [
            'type'       => null,
            'label'      => 'Users',
            'single'     => 'User',
            'create'     => false,
            'assignable' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Screens
    |--------------------------------------------------------------------------
    |
    | Three managers, and one matrix reached from the second:
    |
    |   rules        the guard names the application checks against
    |   owners       the owner records, and from there their permissions
    |   permissions  allow or forbid each rule for one owner, with conditions
    |   inherit      who inherits from whom
    |   explain      why a check answers what it answers
    |   health       stored conditions and rules against the models of today
    |   xacml        export and import of policies
    |
    | Each can be switched off, and each write can be put behind a Gate ability.
    | A screen that is off is hidden AND its endpoints answer 403, so the markup
    | is never the only thing standing in the way.
    |
    | `ability` null means "no extra check beyond the route middleware".
    |
    | Worth a thought for rules: they are the vocabulary the rest of the
    | application checks against, so a renamed guard name silently stops
    | matching the code that asks for it. Setting `write` to false leaves the
    | tree browsable while writes answer 403; rules then change only through
    | migrations, which is where a vocabulary belongs.
    |
    */
    'screens' => [
        'rules'       => ['enabled' => true, 'write' => true, 'ability' => null],
        'owners'      => ['enabled' => true, 'write' => true, 'ability' => null],
        'permissions' => ['enabled' => true, 'write' => true, 'ability' => null],
        'inherit'     => ['enabled' => true, 'write' => true, 'ability' => null],

        // New in 3.0. "explain" shows rules of every owner, "health" checks stored conditions
        // against models, "xacml" exports and imports the whole set. They are on, like the
        // rest: whoever passed the middleware of the group administers access. Name an
        // ability here to narrow one of them to fewer people.
        'explain'     => ['enabled' => true, 'write' => true, 'ability' => null],
        'health'      => ['enabled' => true, 'write' => true, 'ability' => null],
        'xacml'       => ['enabled' => true, 'write' => true, 'ability' => null],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pickers
    |--------------------------------------------------------------------------
    |
    | Choosing an owner out of a long list is paged and searched server-side.
    | Short lists are sent inline with the screen instead, so the common case
    | costs no extra request.
    |
    */
    'picker' => [

        // Rows per page when searching.
        'per_page' => 15,

        // Send the whole list with the screen while it is no longer than this.
        'inline_limit' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | XACML
    |--------------------------------------------------------------------------
    |
    | The largest policy an upload may carry, in kilobytes. The core refuses
    | DOCTYPE and never reads the network, so the size is the one thing left
    | to bound here.
    |
    */
    'xacml' => [
        'max_upload_kb' => 10240,
    ],
];
