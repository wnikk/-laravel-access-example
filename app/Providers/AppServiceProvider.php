<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Wnikk\LaravelAccessRules\Events\AccessChanged;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerAccessUiGate();
        $this->registerAccessAuditLog();

        // Example 18: the tables store "product", not the class name. The package needs nothing
        // for it, the relations write the type into their subqueries themselves.
        Relation::enforceMorphMap([
            'product'  => Product::class,
            'category' => Category::class,
        ]);
    }

    /**
     * The package keeps no audit log: what to record and for how long is a decision of the
     * application. Every change of access fires one event, and a log is a listener.
     * /example13/audit shows the file.
     */
    protected function registerAccessAuditLog(): void
    {
        Event::listen(AccessChanged::class, function (AccessChanged $event): void {
            Log::build(['driver' => 'single', 'path' => storage_path('logs/access-audit.log')])
                ->info($event->action, $event->details + ['by' => auth()->id()]);
        });
    }

    /**
     * The ability guarding everything that shows or changes access: examples 9, 13
     * and 14 here, the list of users, the profile of somebody else, and the panel of
     * wnikk/laravel-access-ui. config/accessUi.php puts `can:manage-access` on the whole
     * route group of the panel. That check is the only thing between a signed-in account
     * and the screens that hand out every permission in the system, so `['web', 'auth']`
     * alone would not do.
     *
     * The rule is self-hosted — `system.access.manage` is itself an
     * access-rules rule, created by CreateRulesSeeder and granted to the
     * "root" role by CreateRootAdminRoleSeeder.
     */
    protected function registerAccessUiGate(): void
    {
        Gate::define('manage-access', function (User $user): bool {
            if ($user->hasPermission('system.access.manage')) {
                return true;
            }

            // Sandbox escape hatch. A self-hosted permission locks you out of
            // the very screen that grants it when the seeders have not run, so
            // the first user gets in while developing locally.
            //
            // DO NOT keep this in a real application: delete it and grant
            // `system.access.manage` to whoever should administer access.
            return $this->app->environment('local') && (int) $user->getKey() === 1;
        });
    }
}
