<?php

namespace App\Maxitsa\Repository;

require_once __DIR__ . '/../../app/core/abstract/AbstractRepository.php';
require_once __DIR__ . '/../entity/Transaction.php';

use App\Maxitsa\Entity\Transaction;
use PDO;
use DateTime;
use Exception;

class TransactionRepository extends \AbstractRepository
{
    protected string $table = 'transactions';
    protected string $entityClass = Transaction::class;

    public function findByAccountId(int $accountId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE account_id = :account_id 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':account_id', $accountId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'hydrate'], $results);
    }

    public function findByAccountIdWithFilters(int $accountId, ?string $type = null, ?string $dateFrom = null, ?string $dateTo = null, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE account_id = :account_id";
        $params = ['account_id' => $accountId];

        if ($type) {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }

        if ($dateFrom) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'hydrate'], $results);
    }

    public function countByAccountIdWithFilters(int $accountId, ?string $type = null, ?string $dateFrom = null, ?string $dateTo = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE account_id = :account_id";
        $params = ['account_id' => $accountId];

        if ($type) {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }

        if ($dateFrom) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetchColumn();
    }

    public function findPendingDepositsByAccountId(int $accountId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE account_id = :account_id 
            AND type = :type 
            AND sous_type = :sous_type 
            AND status = :status
            ORDER BY created_at DESC
        ");
        $stmt->execute([
            'account_id' => $accountId,
            'type' => Transaction::TYPE_TRANSFERT,
            'sous_type' => Transaction::SOUS_TYPE_DEPOT,
            'status' => Transaction::STATUS_EN_ATTENTE
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'hydrate'], $results);
    }

    public function cancelTransaction(int $transactionId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id AND status = :current_status
        ");
        return $stmt->execute([
            'status' => Transaction::STATUS_ANNULEE,
            'id' => $transactionId,
            'current_status' => Transaction::STATUS_EN_ATTENTE
        ]);
    }

    public function completeTransaction(int $transactionId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id AND status = :current_status
        ");
        return $stmt->execute([
            'status' => Transaction::STATUS_TERMINEE,
            'id' => $transactionId,
            'current_status' => Transaction::STATUS_EN_ATTENTE
        ]);
    }

    protected function hydrate(array $data): Transaction
    {
        $transaction = new Transaction();
        
        if (isset($data['id'])) $transaction->setId($data['id']);
        if (isset($data['account_id'])) $transaction->setAccountId($data['account_id']);
        if (isset($data['account_destination_id'])) $transaction->setAccountDestinationId($data['account_destination_id']);
        if (isset($data['type'])) $transaction->setType($data['type']);
        if (isset($data['sous_type'])) $transaction->setSousType($data['sous_type']);
        if (isset($data['montant'])) $transaction->setMontant((float)$data['montant']);
        if (isset($data['frais'])) $transaction->setFrais((float)$data['frais']);
        if (isset($data['status'])) $transaction->setStatus($data['status']);
        if (isset($data['description'])) $transaction->setDescription($data['description']);
        
        if (isset($data['created_at'])) {
            $transaction->setCreatedAt(new DateTime($data['created_at']));
        }
        if (isset($data['updated_at'])) {
            $transaction->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $transaction;
    }
}
