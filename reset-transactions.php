<?php

// Chargement de l'autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Chargement des variables d'environnement
require_once __DIR__ . '/app/config/env.php';
require_once __DIR__ . '/app/config/helpers.php';
Env::load(__DIR__ . '/.env');

class TransactionReset
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function reset(): void
    {
        try {
            // Supprimer toutes les transactions
            echo "Suppression des anciennes transactions...\n";
            $this->db->query("DELETE FROM transactions");
            
            // Réexécuter le seeder
            echo "Ajout des nouvelles transactions...\n";
            include __DIR__ . '/seeders/seeder.php';
            $seeder = new Seeder();
            $seeder->seedTransactions();
            
            echo "Transactions mises à jour avec succès!\n";
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage() . "\n";
        }
    }
}

$reset = new TransactionReset();
$reset->reset();
