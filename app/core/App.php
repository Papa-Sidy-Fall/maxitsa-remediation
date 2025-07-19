<?php

class App
{
    private static ?App $instance = null;
    private array $dependencies = [];
    private array $services = [];

    private function __construct()
    {
        $this->loadServices();
        $this->initializeDependencies();
    }

    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getDependency(string $name): mixed
    {
        return self::getInstance()->get($name);
    }

    public function get(string $name): mixed
    {
        if (!isset($this->dependencies[$name])) {
            throw new Exception("Dependency not found: $name");
        }

        return $this->dependencies[$name];
    }

    private function loadServices(): void
    {
        $servicesFile = 'config/services.yml';
        if (file_exists($servicesFile)) {
            $this->services = $this->parseYaml($servicesFile);
        }
    }

    private function parseYaml(string $file): array
    {
        // Simple YAML parser pour les services
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        $services = [];
        $currentService = null;
        $currentCategory = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;

            if (strpos($line, 'services:') === 0) continue;

            if (preg_match('/^(\w+):$/', $line, $matches)) {
                $currentCategory = $matches[1];
                $services[$currentCategory] = [];
            } elseif (preg_match('/^(\w+):$/', $line, $matches) && $currentCategory) {
                $currentService = $matches[1];
                $services[$currentCategory][$currentService] = [];
            } elseif (preg_match('/^class:\s*(\w+)$/', $line, $matches) && $currentService) {
                $services[$currentCategory][$currentService]['class'] = $matches[1];
            } elseif (preg_match('/^singleton:\s*(true|false)$/', $line, $matches) && $currentService) {
                $services[$currentCategory][$currentService]['singleton'] = $matches[1] === 'true';
            }
        }

        return $services;
    }

    private function initializeDependencies(): void
    {
        // Core dependencies
        $this->dependencies = [
            'router' => new Router(),
            'database' => Database::getInstance(),
            'session' => Session::getInstance(),
            'validator' => new Validator(),
            'fileUpload' => new FileUpload(),
        ];
    }

    public function run(): void
    {
        try {
            $router = $this->get('router');
            $router->dispatch();
        } catch (Exception $e) {
            if (env('APP_DEBUG', false)) {
                throw $e;
            } else {
                // Log error and show generic error page
                error_log($e->getMessage());
                http_response_code(500);
                view('errors/500');
            }
        }
    }
}
