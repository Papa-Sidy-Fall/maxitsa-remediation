<?php

class Session
{
    private static ?Session $instance = null;
    private bool $started = false;

    private function __construct()
    {
        // Ne pas appeler start() ici pour éviter la récursion
    }

    public static function getInstance(): Session
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function start(): void
    {
        $instance = self::getInstance();
        if (!$instance->started && session_status() === PHP_SESSION_NONE) {
            session_start();
            $instance->started = true;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    public static function setFlash(string $key, mixed $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['flash'][$key]);
    }

    public static function regenerateId(): void
    {
        session_regenerate_id(true);
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }

    public static function setUser(int $userId): void
    {
        self::set('user_id', $userId);
        self::regenerateId();
    }

    public static function logout(): void
    {
        self::remove('user_id');
        self::destroy();
    }
}
