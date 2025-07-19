<?php

class CryptPassword
{
    public function __invoke(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['password'])) {
                $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($_POST['password_confirmation'])) {
                $_POST['password_confirmation'] = password_hash($_POST['password_confirmation'], PASSWORD_DEFAULT);
            }
        }
    }
}
