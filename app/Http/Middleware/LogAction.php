<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogAction
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
            $loggableId = null;
            $loggableType = null;
            // dd($request->route()->parameters());
            // Extract route parameters dynamically
            foreach ($request->route()->parameters() as $key => $param) {
                if (is_object($param) && method_exists($param, 'getKey')) {
                    $loggableId = $param->getKey();  // Get the primary key of the model
                    $loggableType = get_class($param); // Get the model class name
                    break;
                }
            }

            // Log only if a valid model is found
            if ($loggableId && $loggableType) {
                Log::create([
                    'user_id' => Auth::id(),
                    'action' => strtoupper($request->method()),
                    'loggable_id' => $loggableId,
                    'loggable_type' => $loggableType,
                    'created_at' => now(),
                ]);
            }
        }

        return $response;
    }
}
