<?php

namespace App\Maxitsa\Service;

require_once __DIR__ . '/../repository/AccountRepository.php';
require_once __DIR__ . '/../repository/TransactionRepository.php';
require_once __DIR__ . '/../entity/Account.php';
require_once __DIR__ . '/../entity/Transaction.php';

use App\Maxitsa\Repository\AccountRepository;
use App\Maxitsa\Repository\TransactionRepository;
use App\Maxitsa\Entity\Account;
use App\Maxitsa\Entity\Transaction;
use Exception;

class AccountService
{
    private AccountRepository $accountRepository;
    private TransactionRepository $transactionRepository;

    public function __construct(AccountRepository $accountRepository, TransactionRepository $transactionRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function getUserAccounts(int $userId): array
    {
        return $this->accountRepository->findByUserId($userId);
    }

    public function getPrincipalAccount(int $userId): ?Account
    {
        return $this->accountRepository->findPrincipalByUserId($userId);
    }

    public function getAccountById(int $accountId): ?Account
    {
        return $this->accountRepository->findById($accountId);
    }

    public function createSecondaryAccount(int $userId, string $telephone, float $initialBalance = 0): Account
    {
        // Vérification que le téléphone n'est pas déjà utilisé
        if ($this->accountRepository->telephoneExists($telephone)) {
            throw new Exception("Ce numéro de téléphone est déjà utilisé pour un autre compte.");
        }

        try {
            // Si un solde initial est fourni, faire un transfert depuis le compte principal
            if ($initialBalance > 0) {
                $principalAccount = $this->getPrincipalAccount($userId);
                if (!$principalAccount || !$principalAccount->canDebit($initialBalance)) {
                    throw new Exception("Solde insuffisant sur le compte principal pour l'initialisation.");
                }
            }

            // Créer le compte secondaire
            $account = new Account();
            $account->setUserId($userId);
            $account->setTelephone($telephone);
            $account->setSolde($initialBalance);
            $account->setIsPrincipal(false);

            if (!$this->accountRepository->create($account)) {
                throw new Exception("Erreur lors de la création du compte secondaire.");
            }

            // Si un solde initial est fourni, créer les transactions correspondantes
            if ($initialBalance > 0) {
                $this->createTransferTransactions($principalAccount->getId(), $account->getId(), $initialBalance);
                
                // Débiter le compte principal
                $this->accountRepository->updateSolde(
                    $principalAccount->getId(), 
                    $principalAccount->getSolde() - $initialBalance
                );
            }

            return $account;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function changeAccountToPrincipal(int $accountId, int $userId): bool
    {
        // Vérifier que le compte appartient bien à l'utilisateur
        $account = $this->getAccountById($accountId);
        if (!$account || $account->getUserId() !== $userId) {
            throw new Exception("Compte non trouvé ou non autorisé.");
        }

        if ($account->getIsPrincipal()) {
            throw new Exception("Ce compte est déjà le compte principal.");
        }

        return $this->accountRepository->changeAccountToPrincipal($accountId, $userId);
    }

    public function getAccountBalance(int $accountId): float
    {
        $account = $this->getAccountById($accountId);
        return $account ? $account->getSolde() : 0.0;
    }

    private function createTransferTransactions(int $fromAccountId, int $toAccountId, float $amount): void
    {
        // Transaction de retrait sur le compte principal
        $withdrawalTransaction = new Transaction();
        $withdrawalTransaction->setAccountId($fromAccountId);
        $withdrawalTransaction->setAccountDestinationId($toAccountId);
        $withdrawalTransaction->setType(Transaction::TYPE_TRANSFERT);
        $withdrawalTransaction->setSousType(Transaction::SOUS_TYPE_RETRAIT);
        $withdrawalTransaction->setMontant($amount);
        $withdrawalTransaction->setFrais(0);
        $withdrawalTransaction->setStatus(Transaction::STATUS_TERMINEE);
        $withdrawalTransaction->setDescription('Transfert vers compte secondaire');

        // Transaction de dépôt sur le compte secondaire
        $depositTransaction = new Transaction();
        $depositTransaction->setAccountId($toAccountId);
        $depositTransaction->setAccountDestinationId($fromAccountId);
        $depositTransaction->setType(Transaction::TYPE_TRANSFERT);
        $depositTransaction->setSousType(Transaction::SOUS_TYPE_DEPOT);
        $depositTransaction->setMontant($amount);
        $depositTransaction->setFrais(0);
        $depositTransaction->setStatus(Transaction::STATUS_TERMINEE);
        $depositTransaction->setDescription('Réception depuis compte principal');

        $this->transactionRepository->create($withdrawalTransaction);
        $this->transactionRepository->create($depositTransaction);
    }
}
