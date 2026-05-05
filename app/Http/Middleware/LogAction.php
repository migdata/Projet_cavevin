<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActionLog;

class LogAction
{
    public function handle(Request $request, Closure $next, string $action, string $model = null)
    {
        $response = $next($request);

        // On log seulement si la requête a réussi
        if ($response->getStatusCode() < 400) {
            ActionLog::create([
                'user_id'  => $request->user()?->id,
                'action'   => $action,
                'model'    => $model,
                'model_id' => $request->route('id'),
                'details'  => $request->except(['password']),
                'ip'       => $request->ip(),
            ]);
        }

        return $response;
    }
}