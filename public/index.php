<?php

// Chargement du bootstrap (qui charge l'autoloader)
require_once __DIR__ . '/../app/config/bootstrap.php';

try {
    // Création de l'instance de l'application
    $app = App::getInstance();
    
    // Récupération du router
    $router = App::getDependency('router');
    
    // Chargement des routes
    require_once __DIR__ . '/../routes/route.web.php';
    
    // Démarrage de l'application
    $app->run();
    
} catch (Exception $e) {
    // Gestion des erreurs globales
    if (env('APP_DEBUG', false)) {
        echo "<h1>Erreur de l'application</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Ligne:</strong> " . $e->getLine() . "</p>";
        echo "<h3>Stack trace:</h3>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Erreur interne du serveur</h1>";
        echo "<p>Une erreur est survenue. Veuillez réessayer plus tard.</p>";
    }
    
    // Log de l'erreur
    error_log("Erreur de l'application: " . $e->getMessage() . " dans " . $e->getFile() . " ligne " . $e->getLine());
}
