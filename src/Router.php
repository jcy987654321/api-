<?php

declare(strict_types=1);

namespace App;

use App\Controllers\HomeController;
use App\Controllers\ApiController;
use App\Controllers\AnnouncementController;
use App\Controllers\FeedbackController;
use App\Controllers\SitemapController;
use App\Controllers\RssController;
use App\Controllers\RobotsController;
use App\Controllers\AdminController;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['pattern'], $uri)) {
                $this->executeRoute($route, $uri);
                return;
            }
        }

        // 404 - Not Found
        http_response_code(404);
        $this->render404();
    }

    private function matchRoute(string $pattern, string $uri): bool
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        return preg_match($pattern, $uri);
    }

    private function executeRoute(array $route, string $uri): void
    {
        $pattern = $route['pattern'];
        $handler = $route['handler'];
        $params = [];

        // Extract parameters from URI
        if (strpos($pattern, '{') !== false) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
            preg_match('#^' . $pattern . '$#', $uri, $matches);
            array_shift($matches); // Remove full match
            
            // Get parameter names from pattern
            preg_match_all('/\{([^}]+)\}/', $route['pattern'], $paramNames);
            $params = array_combine($paramNames[1], $matches);
        }

        // Execute handler
        if (is_array($handler)) {
            $controller = new $handler[0]();
            $method = $handler[1];
            
            if (!empty($params)) {
                $controller->$method(...array_values($params));
            } else {
                $controller->$method();
            }
        } elseif (is_callable($handler)) {
            call_user_func($handler, $params);
        }
    }

    private function render404(): void
    {
        $controller = new \App\Controllers\BaseController();
        $controller->renderView('404', ['page_type' => 'error']);
    }

    private function registerRoutes(): void
    {
        // Static routes
        $this->addRoute('GET', '/', [HomeController::class, 'index']);
        $this->addRoute('GET', '/feedback', [FeedbackController::class, 'index']);
        $this->addRoute('POST', '/feedback', [FeedbackController::class, 'submit']);
        
        // API routes
        $this->addRoute('GET', '/apis', [ApiController::class, 'index']);
        $this->addRoute('GET', '/api/{slug}', [ApiController::class, 'detail']);
        
        // Announcement routes
        $this->addRoute('GET', '/announcements', [AnnouncementController::class, 'index']);
        $this->addRoute('GET', '/announcement/{slug}', [AnnouncementController::class, 'detail']);
        
        // Admin routes
        $this->addRoute('GET', '/admin', [AdminController::class, 'dashboard']);
        $this->addRoute('GET', '/admin/dashboard', [AdminController::class, 'dashboard']);
        $this->addRoute('GET', '/admin/seo-settings', [AdminController::class, 'seoSettings']);
        $this->addRoute('POST', '/admin/seo-settings', [AdminController::class, 'seoSettings']);
        
        // SEO routes
        $this->addRoute('GET', '/sitemap.xml', [SitemapController::class, 'index']);
        $this->addRoute('POST', '/admin/sitemap/regenerate', [AdminController::class, 'regenerateSitemap']);
        $this->addRoute('GET', '/rss.xml', [RssController::class, 'index']);
        $this->addRoute('POST', '/admin/rss/regenerate', [AdminController::class, 'regenerateRss']);
        $this->addRoute('GET', '/robots.txt', [RobotsController::class, 'index']);
    }

    private function addRoute(string $method, string $pattern, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
}