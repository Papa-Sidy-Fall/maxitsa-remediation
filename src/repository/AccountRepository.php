<?php

namespace App\Maxitsa\Repository;

require_once __DIR__ . '/../../app/core/abstract/AbstractRepository.php';
require_once __DIR__ . '/../entity/Account.php';

use App\Maxitsa\Entity\Account;
use PDO;
use DateTime;
use Exception;

class AccountRepository extends \AbstractRepository
{
    protected string $table = 'accounts';
    protected string $entityClass = Account::class;

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY is_principal DESC, created_at ASC");
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'hydrate'], $results);
    }

    public function findPrincipalByUserId(int $userId): ?Account
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id AND is_principal = true");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $this->hydrate($result) : null;
    }

    public function findByTelephone(string $telephone): ?Account
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE telephone = :telephone");
        $stmt->execute(['telephone' => $telephone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $this->hydrate($result) : null;
    }

    public function telephoneExists(string $telephone): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE telephone = :telephone");
        $stmt->execute(['telephone' => $telephone]);
        return $stmt->fetchColumn() > 0;
    }

    public function changeAccountToPrincipal(int $accountId, int $userId): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Désactiver l'ancien compte principal
            $stmt = $this->db->prepare("UPDATE {$this->table} SET is_principal = false WHERE user_id = :user_id AND is_principal = true");
            $stmt->execute(['user_id' => $userId]);
            
            // Activer le nouveau compte principal
            $stmt = $this->db->prepare("UPDATE {$this->table} SET is_principal = true WHERE id = :account_id AND user_id = :user_id");
            $result = $stmt->execute(['account_id' => $accountId, 'user_id' => $userId]);
            
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateSolde(int $accountId, float $newSolde): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET solde = :solde, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['solde' => $newSolde, 'id' => $accountId]);
    }

    public function transferFunds(int $fromAccountId, int $toAccountId, float $amount, float $fees = 0): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Débiter le compte source
            $stmt = $this->db->prepare("UPDATE {$this->table} SET solde = solde - :amount WHERE id = :id AND solde >= :amount");
            $result1 = $stmt->execute(['amount' => $amount + $fees, 'id' => $fromAccountId]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception("Solde insuffisant ou compte introuvable");
            }
            
            // Créditer le compte destination
            $stmt = $this->db->prepare("UPDATE {$this->table} SET solde = solde + :amount WHERE id = :id");
            $result2 = $stmt->execute(['amount' => $amount, 'id' => $toAccountId]);
            
            if (!$result1 || !$result2) {
                throw new Exception("Erreur lors du transfert");
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    protected function hydrate(array $data): Account
    {
        $account = new Account();
        
        if (isset($data['id'])) $account->setId($data['id']);
        if (isset($data['user_id'])) $account->setUserId($data['user_id']);
        if (isset($data['telephone'])) $account->setTelephone($data['telephone']);
        if (isset($data['solde'])) $account->setSolde((float)$data['solde']);
        if (isset($data['is_principal'])) $account->setIsPrincipal((bool)$data['is_principal']);
        
        if (isset($data['created_at'])) {
            $account->setCreatedAt(new DateTime($data['created_at']));
        }
        if (isset($data['updated_at'])) {
            $account->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $account;
    }
}
