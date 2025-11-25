<?php

namespace ElevateCommerce\VisualEditor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeImages
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process HTML responses
        if ($response->headers->get('Content-Type') && 
            strpos($response->headers->get('Content-Type'), 'text/html') !== false) {
            
            $content = $response->getContent();
            
            // Add fetchpriority="high" to first image (LCP candidate)
            $content = preg_replace(
                '/(<img[^>]*)(>)/i',
                '$1 fetchpriority="high" loading="eager"$2',
                $content,
                1 // Only first image
            );
            
            // Add loading="lazy" to all other images
            $content = preg_replace_callback(
                '/<img(?![^>]*loading=)[^>]*>/i',
                function ($matches) {
                    return str_replace('<img', '<img loading="lazy"', $matches[0]);
                },
                $content
            );
            
            $response->setContent($content);
        }

        return $response;
    }
}
