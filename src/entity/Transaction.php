<?php

namespace App\Maxitsa\Entity;

use DateTime;
use ReflectionClass;
use InvalidArgumentException;
use AbstractEntity;

class Transaction extends \AbstractEntity
{
    private int $account_id;
    private ?int $account_destination_id;
    private string $type; // 'Paiement' ou 'Transfert'
    private ?string $sous_type; // 'Dépôt' ou 'Retrait' pour les transferts
    private float $montant;
    private float $frais;
    private string $status; // 'en_attente', 'terminée', 'annulée'
    private ?string $description;

    // Constants
    public const TYPE_PAIEMENT = 'Paiement';
    public const TYPE_TRANSFERT = 'Transfert';
    
    public const SOUS_TYPE_DEPOT = 'Dépôt';
    public const SOUS_TYPE_RETRAIT = 'Retrait';
    
    public const STATUS_EN_ATTENTE = 'en_attente';
    public const STATUS_TERMINEE = 'terminée';
    public const STATUS_ANNULEE = 'annulée';

    // Getters
    public function getAccountId(): int
    {
        return $this->account_id;
    }

    public function getAccountDestinationId(): ?int
    {
        return $this->account_destination_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSousType(): ?string
    {
        return isset($this->sous_type) ? $this->sous_type : null;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getFrais(): float
    {
        return $this->frais;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMontantFormatted(): string
    {
        return formatCurrency($this->montant);
    }

    public function getFraisFormatted(): string
    {
        return formatCurrency($this->frais);
    }

    public function getMontantTotal(): float
    {
        return $this->montant + $this->frais;
    }

    public function getMontantTotalFormatted(): string
    {
        return formatCurrency($this->getMontantTotal());
    }

    // Setters
    public function setAccountId(int $accountId): void
    {
        $this->account_id = $accountId;
    }

    public function setAccountDestinationId(?int $accountDestinationId): void
    {
        $this->account_destination_id = $accountDestinationId;
    }

    public function setType(string $type): void
    {
        if (!in_array($type, [self::TYPE_PAIEMENT, self::TYPE_TRANSFERT])) {
            throw new InvalidArgumentException("Type de transaction invalide: $type");
        }
        $this->type = $type;
    }

    public function setSousType(?string $sousType): void
    {
        if ($sousType !== null && !in_array($sousType, [self::SOUS_TYPE_DEPOT, self::SOUS_TYPE_RETRAIT])) {
            throw new InvalidArgumentException("Sous-type de transaction invalide: $sousType");
        }
        $this->sous_type = $sousType;
    }

    public function setMontant(float $montant): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException("Le montant doit être positif");
        }
        $this->montant = $montant;
    }

    public function setFrais(float $frais): void
    {
        if ($frais < 0) {
            throw new InvalidArgumentException("Les frais ne peuvent pas être négatifs");
        }
        $this->frais = $frais;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, [self::STATUS_EN_ATTENTE, self::STATUS_TERMINEE, self::STATUS_ANNULEE])) {
            throw new InvalidArgumentException("Statut de transaction invalide: $status");
        }
        $this->status = $status;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    // Business methods
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    public function isTransfer(): bool
    {
        return $this->type === self::TYPE_TRANSFERT;
    }

    public function isPayment(): bool
    {
        return $this->type === self::TYPE_PAIEMENT;
    }

    public function isDeposit(): bool
    {
        return $this->isTransfer() && $this->sous_type === self::SOUS_TYPE_DEPOT;
    }

    public function isWithdrawal(): bool
    {
        return $this->isTransfer() && $this->sous_type === self::SOUS_TYPE_RETRAIT;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_EN_ATTENTE => 'badge-warning',
            self::STATUS_TERMINEE => 'badge-success',
            self::STATUS_ANNULEE => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}
