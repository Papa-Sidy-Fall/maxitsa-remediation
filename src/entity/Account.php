<?php

namespace App\Maxitsa\Entity;

use DateTime;
use ReflectionClass;
use Exception;
use AbstractEntity;

class Account extends \AbstractEntity
{
    private int $user_id;
    private string $telephone;
    private float $solde;
    private bool $is_principal;

    // Getters
    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getSolde(): float
    {
        return $this->solde;
    }

    public function getIsPrincipal(): bool
    {
        return $this->is_principal;
    }

    public function getSoldeFormatted(): string
    {
        return formatCurrency($this->solde);
    }

    // Setters
    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setSolde(float $solde): void
    {
        $this->solde = $solde;
    }

    public function setIsPrincipal(bool $isPrincipal): void
    {
        $this->is_principal = $isPrincipal;
    }

    // Business methods
    public function canDebit(float $amount): bool
    {
        return $this->solde >= $amount;
    }

    public function debit(float $amount): void
    {
        if (!$this->canDebit($amount)) {
            throw new Exception("Solde insuffisant pour débiter {$amount}");
        }
        $this->solde -= $amount;
    }

    public function credit(float $amount): void
    {
        $this->solde += $amount;
    }

    public function getAccountType(): string
    {
        return $this->is_principal ? 'Principal' : 'Secondaire';
    }
}
