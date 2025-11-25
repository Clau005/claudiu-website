<?php

namespace ElevateCommerce\VisualEditor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CacheStaticAssets
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if this is a static asset request
        $path = $request->path();
        
        // Apply cache headers to storage files and build assets
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|avif|svg|css|js|woff|woff2)$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        return $response;
    }
}
