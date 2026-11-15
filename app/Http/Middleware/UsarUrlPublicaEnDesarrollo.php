<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsarUrlPublicaEnDesarrollo
{
    public function handle(Request $request, Closure $next): Response
    {
        $url = parse_url((string) config('app.url'));

        if (app()->isLocal() && ($url['scheme'] ?? null) === 'https' && isset($url['host'])) {
            $puerto = $url['port'] ?? 443;
            $host = $puerto === 443 ? $url['host'] : $url['host'].':'.$puerto;

            $request->headers->set('host', $host);
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', (string) $puerto);
        }

        return $next($request);
    }
}
