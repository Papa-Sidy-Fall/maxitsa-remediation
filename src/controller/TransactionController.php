<?php

namespace App\Maxitsa\Controller;

require_once __DIR__ . '/../../app/core/abstract/AbstractController.php';
require_once __DIR__ . '/../service/TransactionService.php';
require_once __DIR__ . '/../service/AccountService.php';

use App\Maxitsa\Service\TransactionService;
use App\Maxitsa\Service\AccountService;
use Exception;
use Session;

class TransactionController extends \AbstractController
{
    private TransactionService $transactionService;
    private AccountService $accountService;

    public function __construct()
    {
        parent::__construct();
        
        $db = \App::getDependency('database');
        $accountRepository = new \App\Maxitsa\Repository\AccountRepository($db);
        $transactionRepository = new \App\Maxitsa\Repository\TransactionRepository($db);
        
        $this->transactionService = new TransactionService($transactionRepository, $accountRepository);
        $this->accountService = new AccountService($accountRepository, $transactionRepository);
    }

    public function showDepositForm(): void
    {
        $userId = Session::getUserId();
        $accounts = $this->accountService->getUserAccounts($userId);
        
        $this->view('transaction/deposit', [
            'accounts' => $accounts
        ]);
    }

    public function deposit(): void
    {
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF invalide.');
            $this->redirect('/transaction/deposit');
        }

        $rules = [
            'from_account' => 'required|numeric',
            'to_telephone' => 'required|string|phone',
            'montant' => 'required|numeric|min:100',
            'description' => 'string|max:255'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $this->withErrors($this->validator->getErrors());
            $this->withOldInput();
            $this->redirect('/transaction/deposit');
        }

        $fromAccountId = (int)$_POST['from_account'];
        $toTelephone = $_POST['to_telephone'];
        $amount = (float)$_POST['montant'];
        $description = $_POST['description'] ?? '';

        try {
            // Vérifier que le compte source appartient à l'utilisateur
            $fromAccount = $this->accountService->getAccountById($fromAccountId);
            if (!$fromAccount || $fromAccount->getUserId() !== Session::getUserId()) {
                throw new Exception("Compte source non autorisé.");
            }

            // Trouver le compte de destination
            $db = \App::getDependency('database');
            $accountRepository = new \App\Maxitsa\Repository\AccountRepository($db);
            $toAccount = $accountRepository->findByTelephone($toTelephone);
            
            if (!$toAccount) {
                throw new Exception("Numéro de téléphone de destination introuvable.");
            }

            $transaction = $this->transactionService->createDeposit(
                $fromAccountId, 
                $toAccount->getId(), 
                $amount, 
                $description
            );

            $this->withSuccess('Transaction de dépôt créée avec succès !');
            $this->redirect('/dashboard');
        } catch (Exception $e) {
            $this->withError('Erreur lors de la création du dépôt: ' . $e->getMessage());
            $this->withOldInput();
            $this->redirect('/transaction/deposit');
        }
    }

    public function cancelDeposit(string $transactionId): void
    {
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF invalide.');
            $this->redirect('/dashboard');
        }

        $userId = Session::getUserId();

        try {
            $success = $this->transactionService->cancelDeposit((int)$transactionId, $userId);
            
            if ($success) {
                $this->withSuccess('Transaction annulée avec succès.');
            } else {
                $this->withError('Erreur lors de l\'annulation de la transaction.');
            }
        } catch (Exception $e) {
            $this->withError('Erreur: ' . $e->getMessage());
        }

        $this->redirect('/dashboard');
    }

    public function showPaymentForm(): void
    {
        $userId = Session::getUserId();
        $accounts = $this->accountService->getUserAccounts($userId);
        
        $this->view('transaction/payment', [
            'accounts' => $accounts
        ]);
    }

    public function payment(): void
    {
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF invalide.');
            $this->redirect('/transaction/payment');
        }

        $rules = [
            'account_id' => 'required|numeric',
            'montant' => 'required|numeric|min:100',
            'description' => 'required|string|min:5|max:255'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $this->withErrors($this->validator->getErrors());
            $this->withOldInput();
            $this->redirect('/transaction/payment');
        }

        $accountId = (int)$_POST['account_id'];
        $amount = (float)$_POST['montant'];
        $description = $_POST['description'];

        try {
            // Vérifier que le compte appartient à l'utilisateur
            $account = $this->accountService->getAccountById($accountId);
            if (!$account || $account->getUserId() !== Session::getUserId()) {
                throw new Exception("Compte non autorisé.");
            }

            $transaction = $this->transactionService->createPayment($accountId, $amount, $description);

            $this->withSuccess('Paiement effectué avec succès !');
            $this->redirect('/dashboard');
        } catch (Exception $e) {
            $this->withError('Erreur lors du paiement: ' . $e->getMessage());
            $this->withOldInput();
            $this->redirect('/transaction/payment');
        }
    }

    public function pendingDeposits(): void
    {
        $userId = Session::getUserId();
        
        try {
            $principalAccount = $this->accountService->getPrincipalAccount($userId);
            
            if (!$principalAccount) {
                $this->withError('Aucun compte principal trouvé.');
                $this->redirect('/dashboard');
                return;
            }

            $pendingDeposits = $this->transactionService->getPendingDeposits($principalAccount->getId());

            $this->view('transaction/pending', [
                'deposits' => $pendingDeposits
            ]);
        } catch (Exception $e) {
            $this->withError('Erreur lors du chargement des dépôts en attente: ' . $e->getMessage());
            $this->redirect('/dashboard');
        }
    }
}
