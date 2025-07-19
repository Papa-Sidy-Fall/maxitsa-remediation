<?php

class Auth
{
    public function __invoke(): void
    {
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }
    }
}
