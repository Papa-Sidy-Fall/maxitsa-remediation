<?php

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function __construct()
    {
        $this->middlewares = require_once __DIR__ . '/../config/middlewares.php';
    }

    public function get(string $pattern, string $controller, string $action, array $middlewares = []): void
    {
        $this->addRoute('GET', $pattern, $controller, $action, $middlewares);
    }

    public function post(string $pattern, string $controller, string $action, array $middlewares = []): void
    {
        $this->addRoute('POST', $pattern, $controller, $action, $middlewares);
    }

    public function put(string $pattern, string $controller, string $action, array $middlewares = []): void
    {
        $this->addRoute('PUT', $pattern, $controller, $action, $middlewares);
    }

    public function delete(string $pattern, string $controller, string $action, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $pattern, $controller, $action, $middlewares);
    }

    private function addRoute(string $method, string $pattern, string $controller, string $action, array $middlewares): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPattern($route['pattern'], $uri)) {
                $this->executeRoute($route, $uri);
                return;
            }
        }

        // Route non trouvée
        http_response_code(404);
        view('errors/404');
    }

    private function matchPattern(string $pattern, string $uri): bool
    {
        // Convertir le pattern en regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }

    private function executeRoute(array $route, string $uri): void
    {
        // Exécuter les middlewares
        foreach ($route['middlewares'] as $middlewareName) {
            if (isset($this->middlewares[$middlewareName])) {
                $middlewareClass = $this->middlewares[$middlewareName];
                $middleware = new $middlewareClass();
                $middleware();
            }
        }

        // Extraire les paramètres de l'URI
        $params = $this->extractParams($route['pattern'], $uri);

        // Créer le contrôleur et exécuter l'action
        $controllerClass = $route['controller'];
        $action = $route['action'];

        if (!class_exists($controllerClass)) {
            throw new Exception("Controller not found: $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw new Exception("Action not found: $controllerClass::$action");
        }

        call_user_func_array([$controller, $action], $params);
    }

    private function extractParams(string $pattern, string $uri): array
    {
        $patternRegex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $patternRegex = '#^' . $patternRegex . '$#';
        
        preg_match($patternRegex, $uri, $matches);
        array_shift($matches); // Supprimer le match complet
        
        return $matches;
    }

    public function url(string $name, array $params = []): string
    {
        // Implémentation simplifiée pour générer des URLs
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $name;
    }
}
