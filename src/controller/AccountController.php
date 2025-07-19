<?php

namespace App\Maxitsa\Controller;

require_once __DIR__ . '/../../app/core/abstract/AbstractController.php';
require_once __DIR__ . '/../service/AccountService.php';

use App\Maxitsa\Service\AccountService;
use Exception;
use Session;

class AccountController extends \AbstractController
{
    private AccountService $accountService;

    public function __construct()
    {
        parent::__construct();
        
        $db = \App::getDependency('database');
        $accountRepository = new \App\Maxitsa\Repository\AccountRepository($db);
        $transactionRepository = new \App\Maxitsa\Repository\TransactionRepository($db);
        $this->accountService = new AccountService($accountRepository, $transactionRepository);
    }

    public function showCreateForm(): void
    {
        $this->view('account/create');
    }

    public function create(): void
    {
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF invalide.');
            $this->redirect('/account/create');
        }

        $rules = [
            'telephone' => 'required|string|phone|unique:accounts,telephone',
            'solde' => 'numeric|min:0'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $this->withErrors($this->validator->getErrors());
            $this->withOldInput();
            $this->redirect('/account/create');
        }

        $userId = Session::getUserId();
        $telephone = $_POST['telephone'];
        $initialBalance = (float)($_POST['solde'] ?? 0);

        try {
            $account = $this->accountService->createSecondaryAccount($userId, $telephone, $initialBalance);
            
            $this->withSuccess('Compte secondaire créé avec succès !');
            $this->redirect('/dashboard/accounts');
        } catch (Exception $e) {
            $this->withError('Erreur lors de la création du compte: ' . $e->getMessage());
            $this->withOldInput();
            $this->redirect('/account/create');
        }
    }

    public function changeToPrincipal(string $accountId): void
    {
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF invalide.');
            $this->redirect('/dashboard/accounts');
        }

        $userId = Session::getUserId();

        try {
            $success = $this->accountService->changeAccountToPrincipal((int)$accountId, $userId);
            
            if ($success) {
                $this->withSuccess('Le compte a été défini comme compte principal.');
            } else {
                $this->withError('Erreur lors du changement de compte principal.');
            }
        } catch (Exception $e) {
            $this->withError('Erreur: ' . $e->getMessage());
        }

        $this->redirect('/dashboard/accounts');
    }
}
