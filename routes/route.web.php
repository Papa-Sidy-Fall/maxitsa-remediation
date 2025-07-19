<?php

require_once __DIR__ . '/../src/controller/AuthController.php';
require_once __DIR__ . '/../src/controller/DashboardController.php';
require_once __DIR__ . '/../src/controller/AccountController.php';
require_once __DIR__ . '/../src/controller/TransactionController.php';

use App\Maxitsa\Controller\AuthController;
use App\Maxitsa\Controller\DashboardController;
use App\Maxitsa\Controller\AccountController;
use App\Maxitsa\Controller\TransactionController;

// Routes d'authentification
$router->get('/', AuthController::class, 'showLoginForm');
$router->get('/login', AuthController::class, 'showLoginForm');
$router->post('/login', AuthController::class, 'login');
$router->get('/register', AuthController::class, 'showRegisterForm');
$router->post('/register', AuthController::class, 'register');
$router->post('/logout', AuthController::class, 'logout', ['auth']);

// Routes du tableau de bord (protégées)
$router->get('/dashboard', DashboardController::class, 'index', ['auth']);
$router->get('/dashboard/accounts', DashboardController::class, 'accounts', ['auth']);
$router->get('/dashboard/transactions', DashboardController::class, 'transactions', ['auth']);

// Routes de gestion des comptes (protégées)
$router->get('/account/create', AccountController::class, 'showCreateForm', ['auth']);
$router->post('/account/create', AccountController::class, 'create', ['auth']);
$router->post('/account/{id}/make-principal', AccountController::class, 'changeToPrincipal', ['auth']);

// Routes de gestion des transactions (protégées)
$router->get('/transaction/deposit', TransactionController::class, 'showDepositForm', ['auth']);
$router->post('/transaction/deposit', TransactionController::class, 'deposit', ['auth']);
$router->post('/transaction/{id}/cancel', TransactionController::class, 'cancelDeposit', ['auth']);
$router->get('/transaction/payment', TransactionController::class, 'showPaymentForm', ['auth']);
$router->post('/transaction/payment', TransactionController::class, 'payment', ['auth']);
$router->get('/transaction/pending', TransactionController::class, 'pendingDeposits', ['auth']);
