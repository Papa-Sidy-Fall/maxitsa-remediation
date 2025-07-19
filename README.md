# MAXITSA - Application de Transfert et Paiements

Application web de gestion de comptes et transactions bancaires développée en PHP avec architecture POO respectant les principes SOLID.

## Fonctionnalités

### User Stories Implémentées

- **US1** : Création de compte principal avec carte d'identité et photos
- **US2** : Ajout de comptes secondaires avec numéro de téléphone uniquement
- **US3** : Connexion et consultation du solde du compte principal
- **US4** : Consultation des 10 dernières transactions
- **US5** : Changement d'un compte secondaire en compte principal
- **US7** : Liste complète des transactions avec filtres par date/type et pagination
- **US8** : Transactions de dépôt avec calcul automatique des frais
- **US9** : Annulation des dépôts en attente

### Règles de Gestion

- Premier compte créé = compte principal par défaut
- Comptes secondaires : numéro téléphone uniquement + solde optionnel
- Transferts entre comptes principaux différents : frais de 8% (plafonné à 5000 FCFA)
- Interface responsive et validation des formulaires
- Gestion des photos recto/verso de carte d'identité

## Architecture Technique

### Structure des Dossiers

```
MAXITSA/
├── app/
│   ├── config/           # Configuration et helpers
│   └── core/             # Classes core (App, Database, Router, etc.)
│       ├── middlewares/  # Middlewares d'authentification
│       └── abstract/     # Classes abstraites
├── src/
│   ├── entity/          # Entités métier
│   ├── repository/      # Accès aux données
│   ├── service/         # Logique métier
│   └── controller/      # Contrôleurs
├── routes/              # Définition des routes
├── templates/           # Vues Twig/PHP
├── migrations/          # Scripts de migration DB
├── seeders/            # Données de test
├── public/             # Point d'entrée web
└── config/             # Configuration des services
```

### Patterns Utilisés

- **Singleton** : Database, Session, App
- **Repository Pattern** : Accès aux données
- **Service Layer** : Logique métier
- **MVC** : Séparation des responsabilités
- **Dependency Injection** : Gestion des dépendances via services.yml

## Installation

### Prérequis

- PHP 8.0+
- PostgreSQL 12+
- Serveur web (Apache/Nginx)
- Composer

### Étapes d'Installation

1. **Cloner le projet**
   ```bash
   git clone <repository>
   cd MAXITSA
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer la base de données**
   ```bash
   # Copier le fichier d'environnement
   cp .env.exemple .env
   
   # Éditer .env avec vos paramètres de base de données
   nano .env
   ```

4. **Créer la base de données**
   ```sql
   CREATE DATABASE maxitsa;
   ```

5. **Exécuter les migrations**
   ```bash
   composer database:migrate
   ```

6. **Insérer les données de test (optionnel)**
   ```bash
   composer database:seed
   ```

7. **Configurer le serveur web**
   - Pointer le DocumentRoot vers le dossier `public/`
   - Activer mod_rewrite pour Apache
   - Configurer les permissions d'écriture sur `public/images/uploads/`

### Configuration Apache (Exemple)

```apache
<VirtualHost *:80>
    ServerName maxitsa.local
    DocumentRoot /path/to/MAXITSA/public
    
    <Directory /path/to/MAXITSA/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/maxitsa_error.log
    CustomLog ${APACHE_LOG_DIR}/maxitsa_access.log combined
</VirtualHost>
```

## Utilisation

### Comptes de Test (après seeding)

1. **Birane Baila Wane**
   - Téléphone : 771234567
   - Mot de passe : password123

2. **Papa Sidy Fall**
   - Téléphone : 772345678
   - Mot de passe : password123

3. **Aly Tall Niang**
   - Téléphone : 773456789
   - Mot de passe : password123

### Commandes Utiles

```bash
# Migrer la base de données
composer database:migrate

# Générer des données de test
composer database:seed

# Vérifier la configuration
php -S localhost:8000 -t public/
```

## Structure de la Base de Données

### Tables

- **users** : Informations utilisateurs avec carte d'identité
- **accounts** : Comptes bancaires (principal/secondaire)
- **transactions** : Historique des transactions

### Relations

- User → Accounts (1:N)
- Account → Transactions (1:N)
- Transaction → Account destination (N:1, optionnel)

## Sécurité

- Protection CSRF sur tous les formulaires
- Validation des uploads de fichiers
- Hashage des mots de passe (PASSWORD_DEFAULT)
- Sessions sécurisées avec régénération d'ID
- Validation des entrées utilisateur
- Protection XSS et injection SQL

## Développement

### Ajouter une Nouvelle Fonctionnalité

1. Créer l'entité dans `src/entity/`
2. Implémenter le repository dans `src/repository/`
3. Créer le service métier dans `src/service/`
4. Développer le contrôleur dans `src/controller/`
5. Ajouter les routes dans `routes/route.web.php`
6. Créer les templates dans `templates/`

### Tests

Pour tester l'application :

1. Créer un compte principal avec photos
2. Se connecter et consulter le tableau de bord
3. Créer des comptes secondaires
4. Effectuer des transferts et paiements
5. Tester les filtres de transactions
6. Vérifier l'annulation des dépôts

## Support

Pour tout problème ou question :
- Vérifier les logs d'erreur
- Consulter la configuration `.env`
- S'assurer que les permissions sont correctes
- Vérifier la connectivité à la base de données

## Licence

Propriétaire - Papa Sidy Fall
