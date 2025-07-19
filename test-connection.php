<?php

// Test simple de connexion à la base de données
require_once __DIR__ . '/app/config/bootstrap.php';
require_once __DIR__ . '/app/core/Database.php';

try {
    echo "🔄 Test de connexion à PostgreSQL...\n";
    
    $db = Database::getInstance();
    echo "✅ Connexion à la base de données réussie !\n";
    
    // Test d'une requête simple
    $stmt = $db->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if ($result['test'] == 1) {
        echo "✅ Requête de test réussie !\n";
    }
    
    // Test des tables
    $stmt = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll();
    
    echo "📋 Tables trouvées dans la base :\n";
    foreach ($tables as $table) {
        echo "  - " . $table['table_name'] . "\n";
    }
    
    if (count($tables) === 0) {
        echo "⚠️  Aucune table trouvée. Exécutez 'composer database:migrate' pour créer les tables.\n";
    } else {
        echo "✅ Base de données configurée correctement !\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage() . "\n";
    echo "💡 Vérifiez votre configuration dans le fichier .env\n";
}
