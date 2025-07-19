<?php

namespace App\Maxitsa\Controller;

require_once __DIR__ . '/../../app/core/abstract/AbstractController.php';
require_once __DIR__ . '/../service/UserService.php';

use App\Maxitsa\Service\UserService;
use Exception;
use Session;

class AuthController extends \AbstractController
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        
        $db = \App::getDependency('database');
        $userRepository = new \App\Maxitsa\Repository\UserRepository($db);
        $accountRepository = new \App\Maxitsa\Repository\AccountRepository($db);
        $this->userService = new UserService($userRepository, $accountRepository);
    }

    public function showLoginForm(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF invalide.');
            $this->redirect('/login');
        }

        $rules = [
            'telephone' => 'required|string',
            'password' => 'required|string'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $this->withErrors($this->validator->getErrors());
            $this->withOldInput();
            $this->redirect('/login');
        }

        try {
            $user = $this->userService->authenticateUser($_POST['telephone'], $_POST['password']);
            
            if ($user) {
                Session::setUser($user->getId());
                $this->withSuccess('Connexion réussie !');
                $this->redirect('/dashboard');
            } else {
                $this->withError('Identifiants incorrects.');
                $this->withOldInput();
                $this->redirect('/login');
            }
        } catch (Exception $e) {
            $this->withError('Erreur lors de la connexion: ' . $e->getMessage());
            $this->redirect('/login');
        }
    }

    public function showRegisterForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/register');
    }

    public function register(): void
    {
        // Debug temporaire - on désactive le CSRF pour tester
        // if (!$this->validateCsrf()) {
        //     $this->withError('Token CSRF invalide.');
        //     $this->redirect('/register');
        // }

        $rules = [
            'nom' => 'required|string|min:2|max:100',
            'prenom' => 'required|string|min:2|max:100',
            'telephone' => 'required|string|phone|unique:users,telephone',
            'carte_identite' => 'required|string|unique:users,carte_identite',
            'adresse' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            error_log('Validation failed: ' . json_encode($this->validator->getErrors()));
            $this->withErrors($this->validator->getErrors());
            $this->withOldInput();
            $this->redirect('/register');
        }

        // Validation des fichiers
        if (!isset($_FILES['photo_recto']) || !isset($_FILES['photo_verso'])) {
            error_log('Files missing: ' . json_encode($_FILES));
            $this->withError('Les photos recto et verso de la carte d\'identité sont obligatoires.');
            $this->withOldInput();
            $this->redirect('/register');
        }

        try {
            $user = $this->userService->createUser($_POST, $_FILES);
            
            Session::setUser($user->getId());
            $this->withSuccess('Compte créé avec succès !');
            $this->redirect('/dashboard');
        } catch (Exception $e) {
            error_log('Registration exception: ' . $e->getMessage());
            $this->withError('Erreur lors de la création du compte: ' . $e->getMessage());
            $this->withOldInput();
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        Session::logout();
        $this->withSuccess('Déconnexion réussie.');
        $this->redirect('/login');
    }
}
