<?php

namespace App\Maxitsa\Controller;

require_once __DIR__ . '/../../app/core/abstract/AbstractController.php';
require_once __DIR__ . '/../service/UserService.php';
require_once __DIR__ . '/../service/AccountService.php';
require_once __DIR__ . '/../service/TransactionService.php';

use App\Maxitsa\Service\UserService;
use App\Maxitsa\Service\AccountService;
use App\Maxitsa\Service\TransactionService;
use Exception;
use Session;

class DashboardController extends \AbstractController
{
    private UserService $userService;
    private AccountService $accountService;
    private TransactionService $transactionService;

    public function __construct()
    {
        parent::__construct();
        
        $db = \App::getDependency('database');
        $userRepository = new \App\Maxitsa\Repository\UserRepository($db);
        $accountRepository = new \App\Maxitsa\Repository\AccountRepository($db);
        $transactionRepository = new \App\Maxitsa\Repository\TransactionRepository($db);
        
        $this->userService = new UserService($userRepository, $accountRepository);
        $this->accountService = new AccountService($accountRepository, $transactionRepository);
        $this->transactionService = new TransactionService($transactionRepository, $accountRepository);
    }

    public function index(): void
    {
        $userId = Session::getUserId();
        
        try {
            $user = $this->userService->getUserById($userId);
            $principalAccount = $this->accountService->getPrincipalAccount($userId);
            $recentTransactions = [];
            
            if ($principalAccount) {
                $recentTransactions = $this->transactionService->getAccountTransactions($principalAccount->getId(), 10);
            }

            $this->view('dashboard/index', [
                'user' => $user,
                'account' => $principalAccount,
                'transactions' => $recentTransactions
            ]);
        } catch (Exception $e) {
            $this->withError('Erreur lors du chargement du tableau de bord: ' . $e->getMessage());
            $this->redirect('/login');
        }
    }

    public function accounts(): void
    {
        $userId = Session::getUserId();
        
        try {
            $user = $this->userService->getUserById($userId);
            $accounts = $this->accountService->getUserAccounts($userId);

            $this->view('dashboard/accounts', [
                'user' => $user,
                'accounts' => $accounts
            ]);
        } catch (Exception $e) {
            $this->withError('Erreur lors du chargement des comptes: ' . $e->getMessage());
            $this->redirect('/dashboard');
        }
    }

    public function transactions(): void
    {
        $userId = Session::getUserId();
        $page = (int)($_GET['page'] ?? 1);
        $type = $_GET['type'] ?? null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        
        try {
            $principalAccount = $this->accountService->getPrincipalAccount($userId);
            
            if (!$principalAccount) {
                $this->withError('Aucun compte principal trouvé.');
                $this->redirect('/dashboard');
                return;
            }

            $result = $this->transactionService->getAccountTransactionsWithFilters(
                $principalAccount->getId(), $type, $dateFrom, $dateTo, $page, 20
            );

            $this->view('dashboard/transactions', [
                'transactions' => $result['transactions'],
                'pagination' => [
                    'current_page' => $result['page'],
                    'total_pages' => $result['totalPages'],
                    'total' => $result['total'],
                    'per_page' => $result['perPage']
                ],
                'filters' => [
                    'type' => $type,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]
            ]);
        } catch (Exception $e) {
            $this->withError('Erreur lors du chargement des transactions: ' . $e->getMessage());
            $this->redirect('/dashboard');
        }
    }
}
