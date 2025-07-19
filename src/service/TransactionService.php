<?php

namespace App\Maxitsa\Service;

require_once __DIR__ . '/../repository/TransactionRepository.php';
require_once __DIR__ . '/../repository/AccountRepository.php';
require_once __DIR__ . '/../entity/Transaction.php';

use App\Maxitsa\Repository\TransactionRepository;
use App\Maxitsa\Repository\AccountRepository;
use App\Maxitsa\Entity\Transaction;
use Exception;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    private AccountRepository $accountRepository;

    public function __construct(TransactionRepository $transactionRepository, AccountRepository $accountRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    public function getAccountTransactions(int $accountId, int $limit = 10): array
    {
        return $this->transactionRepository->findByAccountId($accountId, $limit);
    }

    public function getAccountTransactionsWithFilters(int $accountId, ?string $type = null, ?string $dateFrom = null, ?string $dateTo = null, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $transactions = $this->transactionRepository->findByAccountIdWithFilters(
            $accountId, $type, $dateFrom, $dateTo, $perPage, $offset
        );
        
        $total = $this->transactionRepository->countByAccountIdWithFilters(
            $accountId, $type, $dateFrom, $dateTo
        );
        
        return [
            'transactions' => $transactions,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function createDeposit(int $fromAccountId, int $toAccountId, float $amount, string $description = ''): Transaction
    {
        $fromAccount = $this->accountRepository->findById($fromAccountId);
        $toAccount = $this->accountRepository->findById($toAccountId);

        if (!$fromAccount || !$toAccount) {
            throw new Exception("Compte source ou destination introuvable.");
        }

        // Calculer les frais si c'est un transfert entre comptes principaux différents
        $fees = 0;
        if ($fromAccount->getIsPrincipal() && $toAccount->getIsPrincipal() && 
            $fromAccount->getUserId() !== $toAccount->getUserId()) {
            $fees = calculateTransferFee($amount);
        }

        $totalAmount = $amount + $fees;

        if (!$fromAccount->canDebit($totalAmount)) {
            throw new Exception("Solde insuffisant. Montant requis: " . formatCurrency($totalAmount));
        }

        try {
            // Créer la transaction
            $transaction = new Transaction();
            $transaction->setAccountId($fromAccountId);
            $transaction->setAccountDestinationId($toAccountId);
            $transaction->setType(Transaction::TYPE_TRANSFERT);
            $transaction->setSousType(Transaction::SOUS_TYPE_DEPOT);
            $transaction->setMontant($amount);
            $transaction->setFrais($fees);
            $transaction->setStatus(Transaction::STATUS_EN_ATTENTE);
            $transaction->setDescription($description ?: "Dépôt vers {$toAccount->getTelephone()}");

            if (!$this->transactionRepository->create($transaction)) {
                throw new Exception("Erreur lors de la création de la transaction.");
            }

            return $transaction;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancelDeposit(int $transactionId, int $userId): bool
    {
        $transaction = $this->transactionRepository->findById($transactionId);
        
        if (!$transaction) {
            throw new Exception("Transaction introuvable.");
        }

        // Vérifier que la transaction appartient à l'utilisateur
        $account = $this->accountRepository->findById($transaction->getAccountId());
        if (!$account || $account->getUserId() !== $userId) {
            throw new Exception("Transaction non autorisée.");
        }

        if (!$transaction->canBeCancelled()) {
            throw new Exception("Cette transaction ne peut pas être annulée.");
        }

        if (!$transaction->isDeposit()) {
            throw new Exception("Seules les transactions de dépôt peuvent être annulées.");
        }

        return $this->transactionRepository->cancelTransaction($transactionId);
    }

    public function getPendingDeposits(int $accountId): array
    {
        return $this->transactionRepository->findPendingDepositsByAccountId($accountId);
    }

    public function processWithdrawal(int $transactionId, int $userId): bool
    {
        $transaction = $this->transactionRepository->findById($transactionId);
        
        if (!$transaction) {
            throw new Exception("Transaction introuvable.");
        }

        // Vérifier que l'utilisateur peut traiter cette transaction
        $destinationAccount = $this->accountRepository->findById($transaction->getAccountDestinationId());
        if (!$destinationAccount || $destinationAccount->getUserId() !== $userId) {
            throw new Exception("Vous n'êtes pas autorisé à traiter cette transaction.");
        }

        if ($transaction->getStatus() !== Transaction::STATUS_EN_ATTENTE) {
            throw new Exception("Cette transaction n'est plus en attente.");
        }

        try {
            // Effectuer le transfert de fonds
            $this->accountRepository->transferFunds(
                $transaction->getAccountId(),
                $transaction->getAccountDestinationId(),
                $transaction->getMontant(),
                $transaction->getFrais()
            );

            // Marquer la transaction comme terminée
            return $this->transactionRepository->completeTransaction($transactionId);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createPayment(int $accountId, float $amount, string $description): Transaction
    {
        $account = $this->accountRepository->findById($accountId);
        
        if (!$account) {
            throw new Exception("Compte introuvable.");
        }

        if (!$account->canDebit($amount)) {
            throw new Exception("Solde insuffisant.");
        }

        try {
            // Créer la transaction de paiement
            $transaction = new Transaction();
            $transaction->setAccountId($accountId);
            $transaction->setType(Transaction::TYPE_PAIEMENT);
            $transaction->setMontant($amount);
            $transaction->setFrais(0);
            $transaction->setStatus(Transaction::STATUS_TERMINEE);
            $transaction->setDescription($description);

            if (!$this->transactionRepository->create($transaction)) {
                throw new Exception("Erreur lors de la création de la transaction.");
            }

            // Débiter le compte
            $this->accountRepository->updateSolde($accountId, $account->getSolde() - $amount);

            return $transaction;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getTransactionById(int $transactionId): ?Transaction
    {
        return $this->transactionRepository->findById($transactionId);
    }
}
