<?php



abstract class AbstractController
{
    protected Router $router;
    protected Session $session;
    protected Validator $validator;

    public function __construct()
    {
        $this->router = App::getDependency('router');
        $this->session = App::getDependency('session');
        $this->validator = App::getDependency('validator');
    }

    protected function redirect(string $url): void
    {
        redirect($url);
    }

    protected function view(string $template, array $data = []): void
    {
        view($template, $data);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = Session::get('csrf_token');
        
        // Si pas de token en session, on le considère comme invalide
        if ($sessionToken === null) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }

    protected function withErrors(array $errors): void
    {
        Session::setFlash('errors', $errors);
    }

    protected function withOldInput(): void
    {
        Session::setFlash('old', $_POST);
    }

    protected function withSuccess(string $message): void
    {
        Session::setFlash('success', $message);
    }

    protected function withError(string $message): void
    {
        Session::setFlash('error', $message);
    }
}
