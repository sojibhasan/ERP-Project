<?php

namespace App\Http\Middleware;

use Closure;

use Config;
use App;
use Illuminate\Support\Facades\Auth;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = config('app.locale');
        if (!empty(Auth::user()->language)) {
            $locale = Auth::user()->language;
        }
        App::setLocale($locale);

        return $next($request);
    }
}
