<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * The language of the session, chosen with /lang/{locale}. The panel of wnikk/laravel-access-ui
 * follows app()->getLocale(): its bundle picks the messages of that locale when the page
 * registered them, its server answers with lang/vendor/accessUi/{locale}/errors.php and
 * lang/{locale}.json. The package ships English and knows nothing of the Russian kept here.
 */
class SetLocale
{
    public const LOCALES = ['en', 'ru'];

    public function handle(Request $request, Closure $next)
    {
        $locale = (string) $request->session()->get('locale', config('app.locale'));

        app()->setLocale(in_array($locale, self::LOCALES, true) ? $locale : config('app.locale'));

        return $next($request);
    }
}
