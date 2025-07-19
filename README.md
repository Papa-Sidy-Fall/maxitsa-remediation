
3. **Configurer la base de données**
   ```bash
   # Copier le fichier d'environnement
   cp .env.exemple .env
   
   # Éditer .env avec vos paramètres de base de données
   nano .env
   ```

4. **Créer la base de données**
   CREATE DATABASE maxitsa;
   ```
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