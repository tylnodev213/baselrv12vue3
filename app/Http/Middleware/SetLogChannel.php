<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetLogChannel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Phát hiện service từ route prefix
        $routePath = $request->path();
        $channel = 'web'; // Mặc định là web

        // Xác định channel dựa trên path
        if (str_starts_with($routePath, 'admin')) {
            $channel = 'admin';
        } elseif (str_starts_with($routePath, 'api/batch') || str_starts_with($routePath, 'batch')) {
            $channel = 'batch';
        }

        // Lưu channel vào context request để sử dụng trong ứng dụng
        $request->attributes->set('log_channel', $channel);

        return $next($request);
    }
}
