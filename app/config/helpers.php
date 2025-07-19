<?php

function env(string $key, mixed $default = null): mixed
{
    return Env::get($key, $default);
}

function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function view(string $template, array $data = []): void
{
    extract($data);
    $templatePath = __DIR__ . "/../../templates/$template.php";
    
    if (file_exists($templatePath)) {
        include $templatePath;
    } else {
        throw new Exception("Template not found: $templatePath");
    }
}

function csrf_token(): string
{
    if (!Session::has('csrf_token')) {
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }
    return Session::get('csrf_token');
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function old(string $key, string $default = ''): string
{
    return Session::getFlash('old')[$key] ?? $default;
}

function errors(string $key = null): array|string|null
{
    $errors = Session::getFlash('errors', []);
    return $key ? ($errors[$key] ?? null) : $errors;
}

function formatCurrency(float $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

function calculateTransferFee(float $amount): float
{
    $fee = $amount * env('TRANSFER_FEE_RATE', 0.08);
    return min($fee, env('TRANSFER_FEE_MAX', 5000));
}
