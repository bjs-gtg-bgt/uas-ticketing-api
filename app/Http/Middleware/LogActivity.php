<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Hanya log method yang mengubah data (POST, PUT, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            if (Auth::check()) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $request->method() . ' ' . $request->path(),
                    'description' => json_encode($request->all()), // Simpan input data
                    'ip_address' => $request->ip()
                ]);
            }
        }

        return $response;
    }
}