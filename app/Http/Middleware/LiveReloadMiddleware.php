<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class LiveReloadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only inject live reload script in local development environment
        // Using config helper to avoid service container issues during bootstrap
        $appEnv = $_ENV['APP_ENV'] ?? config('app.env') ?? 'production';
        
        if (
            in_array($appEnv, ['local', 'development']) && 
            $response instanceof Response && 
            $request->isMethod('GET') &&
            $response->headers->has('Content-Type') &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            $content = $response->getContent();
            
            // Only inject if response has content, is HTML, and script is not already present
            if ($content && !str_contains($content, 'laravel-livereload-script')) {
                $timestampFile = storage_path('framework/cache/laravel-livereload-timestamp');
                $timestamp = File::exists($timestampFile) ? File::get($timestampFile) : time();
                
                $liveReloadScript = $this->getLiveReloadScript($timestamp);
                
                // Inject the script before the closing body tag
                if (str_contains($content, '</body>')) {
                    $content = str_replace('</body>', $liveReloadScript . '</body>', $content);
                } elseif (str_contains($content, '</html>')) {
                    $content = str_replace('</html>', $liveReloadScript . '</html>', $content);
                }
                
                $response->setContent($content);
            }
        }

        return $response;
    }

    /**
     * Generate the live reload script
     */
    private function getLiveReloadScript($timestamp)
    {
        return <<<HTML

        <!-- Laravel Live Reload Script -->
        <script id="laravel-livereload-script" type="module">
            // Simple file change watcher script
            let lastModified = {$timestamp};
            const checkInterval = 1000; // Check every second

            function checkForChanges() {
                fetch('/_livereload_timestamp?_=' + Date.now())
                    .then(response => {
                        if (response.ok) {
                            return response.text();
                        }
                        throw new Error('Network response was not ok.');
                    })
                    .then(newTimestamp => {
                        if (newTimestamp && newTimestamp.trim() !== lastModified.toString()) {
                            console.log('File change detected, reloading page...');
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        // Ignore errors - may be due to server restart
                        console.log('Live reload check failed:', error.message);
                    });
            }

            // Start checking for changes only after page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setInterval(checkForChanges, checkInterval);
                });
            } else {
                setInterval(checkForChanges, checkInterval);
            }
        </script>
        HTML;
    }
}