<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'ທ່ານບໍ່ມີສິດເຂົ້າໃຊ້ໜ້ານີ້');
        }

        return $next($request);
    }
}
