<?php

// Chargement de l'autoloader de Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Chargement des variables d'environnement
require_once __DIR__ . '/env.php';
Env::load(__DIR__ . '/../../.env');

// Chargement des helpers
require_once __DIR__ . '/helpers.php';

// Démarrage de la session
Session::start();

// Configuration des erreurs selon l'environnement
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuration du timezone
date_default_timezone_set('Africa/Dakar');
