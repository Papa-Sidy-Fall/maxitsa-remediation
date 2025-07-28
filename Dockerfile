FROM php:8.2-apache

# Copie du code dans le dossier web
COPY . /var/www/html/

# Définir le dossier public comme racine du serveur
RUN rm -rf /var/www/html/* && cp -r /var/www/html/public/* /var/www/html/

WORKDIR /var/www/html/

# Activation des extensions PHP nécessaires (exemple : pdo_pgsql pour PostgreSQL)
RUN docker-php-ext-install pdo pdo_pgsql

EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]
