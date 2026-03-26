<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivityApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
      
        if (auth('sanctum')->check()) {

            auth()->setUser(auth('sanctum')->user());
    
            $method = $request->method();         
            $path = $request->path();                 
            $query = $request->getQueryString();       
    
            $data = $request->all();              
    
            $safeData = collect($data)->except([
                'password', 'password_confirmation', '_token', '_method', 'otp', 'token'
            ]);
    
            $safeDataJson = mb_substr(json_encode($safeData, JSON_UNESCAPED_SLASHES), 0, 1000);
    
            $message = strtoupper($method) . ' ' . $path;
            if ($query) {
                $message .= '?' . $query;
            }
    
            $message .= ' | Data: ' . $safeDataJson;
    
            logActivity('Visit Page ' . $path, $message);
        }
    
        return $response;
    }

}
