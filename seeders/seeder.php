<?php

// Chargement de l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Chargement des variables d'environnement
require_once __DIR__ . '/../app/config/env.php';
require_once __DIR__ . '/../app/config/helpers.php';
Env::load();

class Seeder
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function seed(): void
    {
        $this->seedUsers();
        $this->seedAccounts();
        $this->seedTransactions();
        
        echo "Données de test insérées avec succès!\n";
    }

    private function seedUsers(): void
    {
        // Vérifier si des utilisateurs existent déjà
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "Des utilisateurs existent déjà, skip du seeding des utilisateurs.\n";
            return;
        }
        
        $users = [
            [
                'nom' => 'Wane',
                'prenom' => 'Birane Baila',
                'telephone' => '771234567',
                'carte_identite' => 'CI123456789',
                'adresse' => 'Dakar, Plateau',
                'photo_recto' => 'uploads/recto1.jpg',
                'photo_verso' => 'uploads/verso1.jpg',
                'password' => password_hash('password123', PASSWORD_DEFAULT)
            ],
            [
                'nom' => 'Fall',
                'prenom' => 'Aminata',
                'telephone' => '772345678',
                'carte_identite' => 'CI234567890',
                'adresse' => 'Thiès, Centre',
                'photo_recto' => 'uploads/recto2.jpg',
                'photo_verso' => 'uploads/verso2.jpg',
                'password' => password_hash('password123', PASSWORD_DEFAULT)
            ],
            [
                'nom' => 'Ndiaye',
                'prenom' => 'Omar',
                'telephone' => '773456789',
                'carte_identite' => 'CI345678901',
                'adresse' => 'Saint-Louis, Nord',
                'photo_recto' => 'uploads/recto3.jpg',
                'photo_verso' => 'uploads/verso3.jpg',
                'password' => password_hash('password123', PASSWORD_DEFAULT)
            ]
        ];

        $sql = "INSERT INTO users (nom, prenom, telephone, carte_identite, adresse, photo_recto, photo_verso, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        foreach ($users as $user) {
            $stmt->execute(array_values($user));
        }

        echo "Utilisateurs créés.\n";
    }

    private function seedAccounts(): void
    {
        // Vérifier si des comptes existent déjà
        $stmt = $this->db->query("SELECT COUNT(*) FROM accounts");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "Des comptes existent déjà, skip du seeding des comptes.\n";
            return;
        }
        
        $accounts = [
            ['user_id' => 1, 'telephone' => '771234567', 'solde' => 100000.00, 'is_principal' => 1],
            ['user_id' => 1, 'telephone' => '777777777', 'solde' => 50000.00, 'is_principal' => 0],
            ['user_id' => 2, 'telephone' => '772345678', 'solde' => 75000.00, 'is_principal' => 1],
            ['user_id' => 2, 'telephone' => '788888888', 'solde' => 25000.00, 'is_principal' => 0],
            ['user_id' => 3, 'telephone' => '773456789', 'solde' => 120000.00, 'is_principal' => 1],
        ];

        $sql = "INSERT INTO accounts (user_id, telephone, solde, is_principal) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        foreach ($accounts as $account) {
            $stmt->execute(array_values($account));
        }

        echo "Comptes créés.\n";
    }

    public function seedTransactions(): void
    {
        // Vérifier si des transactions existent déjà
        $stmt = $this->db->query("SELECT COUNT(*) FROM transactions");
        $count = $stmt->fetchColumn();
        
        // Commenté pour permettre la recréation
        // if ($count > 0) {
        //     echo "Des transactions existent déjà, skip du seeding des transactions.\n";
        //     return;
        // }
        
        $transactions = [
            [
                'account_id' => 1,
                'account_destination_id' => 3,
                'type' => 'Transfert',
                'sous_type' => 'Dépôt',
                'montant' => 25000.00,
                'frais' => 2000.00,
                'status' => 'terminée',
                'description' => 'Transfert vers Omar Ndiaye'
            ],
            [
                'account_id' => 2,
                'account_destination_id' => null,
                'type' => 'Paiement',
                'sous_type' => null,
                'montant' => 15000.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Paiement facture électricité'
            ],
            [
                'account_id' => 3,
                'account_destination_id' => 1,
                'type' => 'Transfert',
                'sous_type' => 'Retrait',
                'montant' => 30000.00,
                'frais' => 2400.00,
                'status' => 'en_attente',
                'description' => 'Transfert vers Moussa Diop'
            ],
            [
                'account_id' => 1,
                'account_destination_id' => 2,
                'type' => 'Transfert',
                'sous_type' => 'Dépôt',
                'montant' => 40000.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Transfert vers compte secondaire'
            ],
            // 9 nouvelles transactions pour Birane Baila Wane
            [
                'account_id' => 1,
                'account_destination_id' => null,
                'type' => 'Paiement',
                'sous_type' => null,
                'montant' => 8500.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Paiement facture téléphone'
            ],
            [
                'account_id' => 1,
                'account_destination_id' => 3,
                'type' => 'Transfert',
                'sous_type' => 'Dépôt',
                'montant' => 75000.00,
                'frais' => 6000.00,
                'status' => 'terminée',
                'description' => 'Transfert vers Omar pour urgence'
            ],
            [
                'account_id' => 2,
                'account_destination_id' => null,
                'type' => 'Paiement',
                'sous_type' => null,
                'montant' => 12000.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Paiement abonnement internet'
            ],
            [
                'account_id' => 1,
                'account_destination_id' => null,
                'type' => 'Paiement',
                'sous_type' => null,
                'montant' => 25000.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Paiement location appartement'
            ],
            [
                'account_id' => 1,
                'account_destination_id' => 4,
                'type' => 'Transfert',
                'sous_type' => 'Dépôt',
                'montant' => 50000.00,
                'frais' => 4000.00,
                'status' => 'en_attente',
                'description' => 'Transfert vers Aminata Fall'
            ],
            [
                'account_id' => 2,
                'account_destination_id' => null,
                'type' => 'Paiement',
                'sous_type' => null,
                'montant' => 3500.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Paiement transport mensuel'
            ],
            [
                'account_id' => 1,
                'account_destination_id' => null,
                'type' => 'Paiement',
                'sous_type' => null,
                'montant' => 15000.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Paiement facture eau'
            ],
            [
                'account_id' => 1,
                'account_destination_id' => 2,
                'type' => 'Transfert',
                'sous_type' => 'Dépôt',
                'montant' => 80000.00,
                'frais' => 0.00,
                'status' => 'terminée',
                'description' => 'Alimentation compte secondaire'
            ],
            [
                'account_id' => 2,
                'account_destination_id' => 3,
                'type' => 'Transfert',
                'sous_type' => 'Dépôt',
                'montant' => 35000.00,
                'frais' => 2800.00,
                'status' => 'terminée',
                'description' => 'Transfert aide famille Omar'
            ]
        ];

        $sql = "INSERT INTO transactions (account_id, account_destination_id, type, sous_type, montant, frais, status, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        foreach ($transactions as $transaction) {
            $stmt->execute(array_values($transaction));
        }

        echo "Transactions créées.\n";
    }
}

// Exécution du seeder si le script est appelé directement
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    try {
        $seeder = new Seeder();
        $seeder->seed();
    } catch (Exception $e) {
        echo "Erreur lors du seeding: " . $e->getMessage() . "\n";
    }
}
