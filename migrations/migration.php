<?php

// Chargement de l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Chargement des variables d'environnement
require_once __DIR__ . '/../app/config/env.php';
require_once __DIR__ . '/../app/config/helpers.php';
Env::load();

use Exception;

class Migration
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createTables(): void
    {
        $this->createUsersTable();
        $this->createAccountsTable();
        $this->createTransactionsTable();
        
        echo "Tables créées avec succès!\n";
    }

    private function createUsersTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) UNIQUE NOT NULL,
                carte_identite VARCHAR(50) UNIQUE NOT NULL,
                adresse TEXT NOT NULL,
                photo_recto VARCHAR(255) NOT NULL,
                photo_verso VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $this->db->exec($sql);
        
        // Créer un trigger pour updated_at
        $triggerSql = "
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
            
            DROP TRIGGER IF EXISTS update_users_updated_at ON users;
            CREATE TRIGGER update_users_updated_at 
                BEFORE UPDATE ON users 
                FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
        ";
        
        $this->db->exec($triggerSql);
        echo "Table 'users' créée.\n";
    }

    private function createAccountsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS accounts (
                id SERIAL PRIMARY KEY,
                user_id INTEGER NOT NULL,
                telephone VARCHAR(20) UNIQUE NOT NULL,
                solde DECIMAL(15,2) DEFAULT 0.00,
                is_principal BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        
        $this->db->exec($sql);
        
        // Créer les index
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_accounts_user_id ON accounts(user_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_accounts_telephone ON accounts(telephone)");
        
        // Créer le trigger pour updated_at
        $triggerSql = "
            DROP TRIGGER IF EXISTS update_accounts_updated_at ON accounts;
            CREATE TRIGGER update_accounts_updated_at 
                BEFORE UPDATE ON accounts 
                FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
        ";
        
        $this->db->exec($triggerSql);
        echo "Table 'accounts' créée.\n";
    }

    private function createTransactionsTable(): void
    {
        // Créer les types ENUM pour PostgreSQL
        try {
            $this->db->exec("CREATE TYPE transaction_type AS ENUM ('Paiement', 'Transfert')");
        } catch (Exception $e) {
            // Type déjà existant, on continue
        }
        
        try {
            $this->db->exec("CREATE TYPE transaction_sous_type AS ENUM ('Dépôt', 'Retrait')");
        } catch (Exception $e) {
            // Type déjà existant, on continue
        }
        
        try {
            $this->db->exec("CREATE TYPE transaction_status AS ENUM ('en_attente', 'terminée', 'annulée')");
        } catch (Exception $e) {
            // Type déjà existant, on continue
        }
        
        $sql = "
            CREATE TABLE IF NOT EXISTS transactions (
                id SERIAL PRIMARY KEY,
                account_id INTEGER NOT NULL,
                account_destination_id INTEGER NULL,
                type transaction_type NOT NULL,
                sous_type transaction_sous_type NULL,
                montant DECIMAL(15,2) NOT NULL,
                frais DECIMAL(15,2) DEFAULT 0.00,
                status transaction_status DEFAULT 'en_attente',
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
                FOREIGN KEY (account_destination_id) REFERENCES accounts(id) ON DELETE CASCADE
            )
        ";
        
        $this->db->exec($sql);
        
        // Créer les index
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_transactions_account_id ON transactions(account_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_transactions_type ON transactions(type)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_transactions_status ON transactions(status)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_transactions_created_at ON transactions(created_at)");
        
        // Créer le trigger pour updated_at
        $triggerSql = "
            DROP TRIGGER IF EXISTS update_transactions_updated_at ON transactions;
            CREATE TRIGGER update_transactions_updated_at 
                BEFORE UPDATE ON transactions 
                FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
        ";
        
        $this->db->exec($triggerSql);
        echo "Table 'transactions' créée.\n";
    }
}

// Exécution de la migration si le script est appelé directement
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    try {
        $migration = new Migration();
        $migration->createTables();
    } catch (Exception $e) {
        echo "Erreur lors de la migration: " . $e->getMessage() . "\n";
    }
}
