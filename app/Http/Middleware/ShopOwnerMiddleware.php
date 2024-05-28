<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ShopOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

       if(Auth::user() && (Auth::user()->role === 2 || Auth::user()->role === 3)){
        return $next($request);
       }

       abort(401);

    //    return redirect(route('login'))->with('error',Config::get('variable.YOU_DO_NOT_HAVE_ACCESS'));
    }
}
