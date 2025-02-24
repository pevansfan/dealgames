# DealGames - Plateforme d'Annonces pour Jeux Vidéo

![SIte dealgames](project/public/images/screnn-dealgames.png)

DealGames est une application permettant aux internautes de visualiser et de publier des annonces en ligne pour la vente de consoles, jeux et accessoires. Les utilisateurs doivent négocier le prix directement entre eux, aucun prix n’étant affiché sur la plateforme.

## Fonctionnalités

### Gestion des Annonces
- Création d’une annonce
- Modification d’une annonce (par son auteur uniquement)
- Suppression d’une annonce (par son auteur uniquement)
- Affichage de la liste des annonces
- Affichage des détails d’une annonce

### Gestion de l’Espace Membre
- Authentification et déconnexion
- Option "Se souvenir de moi"
- Réinitialisation du mot de passe
- Création de compte avec validation par email

### Gestion du Profil
- Visualisation du profil
- Modification des informations du profil
- Modification du mot de passe

### Gestion des Rôles
- Rôle utilisateur normal (droits limités à ses propres annonces)
- Rôle administrateur (droits sans restriction sur l’application)

## Technologies Utilisées
- **Symfony 5** : Framework PHP principal
- **Docker** : Environnement de développement conteneurisé
- **Nginx** : Serveur web
- **MariaDB** : Base de données
- **phpMyAdmin** : Gestion de base de données
- **MailHog** : Test des emails

## Prérequis
- Docker
- Docker Compose
- Git

## Installation et Configuration

### 1. Cloner le projet
```bash
git clone git@github.com:Webanimus/symfony-dev-docker-base.git
cd symfony-dev-docker-base
```

### 2. Démarrer l’environnement Docker
```bash
docker compose up -d --build
```

### 3. Créer un projet Symfony et installer les dépendances
```bash
docker compose exec php composer create-project symfony/skeleton .
docker compose exec php composer require webapp
```

### 4. Configurer la base de données
Mettez à jour le fichier `.env` avec les informations suivantes :
```dotenv
DATABASE_URL="mysql://symfony:symfony@database:3306/symfony?serverVersion=mariadb-10.11.2"
```

### 5. Définir les permissions adéquates
```bash
docker compose exec php chown -R www-data:www-data var
```

## Accès à l’Application
- Application : http://localhost:8080
- phpMyAdmin : http://localhost:8081
- MailHog : http://localhost:8025

## Commandes Utiles

Démarrer l’environnement :
```bash
docker compose up -d
```

Arrêter l’environnement :
```bash
docker compose down
```

Accéder au conteneur PHP :
```bash
docker compose exec php bash
```

Installer les dépendances Symfony :
```bash
docker compose exec php composer install
```

Vider le cache Symfony :
```bash
docker compose exec php php bin/console cache:clear
```

Compiler les fichiers Sass :
```bash
npm run sass
```

## Structure du Projet

### Dossier `templates`
Ce dossier contient tous les templates Twig utilisés pour le rendu des pages de l'application. Il est organisé comme suit :
- **`ad/`** : Gère les vues liées aux annonces.
  - `create.html.twig` : Formulaire de création d'une annonce.
  - `edit.html.twig` : Modification d'une annonce.
  - `delete.html.twig` : Suppression d'une annonce.
  - `show.html.twig` : Détails d'une annonce.
- **`category/`** : Affichage des articles par catégorie.
  - `list.html.twig` : Liste des catégories.
  - `show.html.twig` : Articles d'une catégorie.
- **`index/`** : Contient la page d'accueil.
  - `index.html.twig` : Page principale affichant les annonces récentes.
- **`partials/`** : Contient des templates réutilisables.
  - `_nav.html.twig` : Barre de navigation.
  - `_footer.html.twig` : Pied de page.
- **`user/`** : Pages liées aux utilisateurs.
  - `profile.html.twig` : Profil utilisateur.
  - `register.html.twig` : Création de compte.
  - `reset_password.html.twig` : Réinitialisation du mot de passe.
  - `login.html.twig` : Authentification.
- **`base.html.twig`** : Fichier de base à partir duquel les autres templates héritent.

### Dossier `public`
Ce dossier est la racine publique de l'application. Il contient :
- **`assets/`** : Contient les fichiers Sass et JavaScript.
  - **`sass/`** : Organisation des styles avec :
    - `base/` : Styles de base (reset, typographie, couleurs).
    - `components/` : Styles des composants réutilisables (boutons, formulaires, cartes).
    - `layout/` : Structure des pages (header, footer, grille).
    - `media-queries/` : Gestion de la réactivité.
    - `pages/` : Styles spécifiques aux pages.
    - `theme/` : Thèmes et variables globales.
    - `main.scss` : Fichier principal important tous les sous-dossiers.
- **`images/`** :
  - `styles/` : Images liées aux styles de l'application.
  - `products/` : Images des produits et annonces.

### Dossier `Controller`
Contient les contrôleurs Symfony responsables de la logique des pages et de la gestion des données.
- **`AdController.php`** : Gestion des annonces.
- **`CategoryController.php`** : Gestion des catégories.
- **`UserController.php`** : Gestion des utilisateurs (inscription, connexion, profil).

## Bonnes Pratiques
- Respect du principe DRY (Don't Repeat Yourself)
- Code bien documenté
- Utilisation de l’anglais pour le nommage des fichiers et variables
- Sécurisation des données utilisateurs et de l’application

## Ressources et Documentation
- [Documentation officielle Symfony 5](https://symfony.com/doc/5.4/index.html)
- [Installation de Docker](https://korben.info/installer-docker-windows-home.html)
- [SymfonyCasts (Tutoriels Symfony)](https://symfonycasts.com/tracks/symfony)
- [Utilisation de Docker avec Symfony](https://yoandev.co/un-environnement-de-d%C3%A9veloppement-symfony-5-avec-docker-et-docker-compose/)

## Dépannage
### Problèmes courants et commandes utiles

#### Gestion de la base de données :
- **Supprimer la base de données** :
  ```bash
  docker compose exec php php bin/console doctrine:database:drop --force
  ```
  *Supprime la base de données. Utile lors d’un reset complet.*

- **Créer la base de données** :
  ```bash
  docker compose exec php php bin/console doctrine:database:create
  ```
  *Crée la base de données si elle n’existe pas.*

- **Créer une migration** :
  ```bash
  docker compose exec php php bin/console make:migration
  ```
  *Génère les fichiers de migration pour mettre à jour le schéma de la base.*

- **Appliquer les migrations** :
  ```bash
  docker compose exec php php bin/console doctrine:migrations:migrate
  ```
  *Met à jour la base de données selon les migrations générées.*

- **Charger les fixtures** :
  ```bash
  docker compose exec php php bin/console doctrine:fixtures:load --no-interaction -vvv
  ```
  *Recharge les données de test. Supprime les données existantes.*

#### Gestion des images et volumes Docker :
- **Supprimer toutes les images Docker** :
  ```bash
  docker rmi $(docker images -q)
  ```
  *Libère de l’espace en supprimant toutes les images Docker.*

- **Supprimer tous les volumes Docker** :
  ```bash
  docker volume rm $(docker volume ls -q)
  ```
  *Supprime les volumes pour réinitialiser les données persistantes.*

#### Cache et métadonnées Doctrine :
- **Vider le cache Doctrine** :
  ```bash
  docker compose exec php php bin/console doctrine:cache:clear-metadata
  ```
  *Efface les métadonnées de Doctrine après modification des entités.*

- **Vider le cache Symfony** :
  ```bash
  docker compose exec php php bin/console cache:clear
  ```
  *Résout des problèmes de cache obsolète.*

#### Gestion des entités et contrôleurs Symfony :
- **Créer une entité** :
  ```bash
  docker compose exec php php bin/console make:entity
  ```
  *Permet de créer ou modifier des entités Doctrine.*

- **Créer un contrôleur** :
  ```bash
  docker compose exec php php bin/console make:controller
  ```
  *Génère un nouveau contrôleur Symfony.*

#### Utilisation de Zenstruck Foundry et des factories :
- **Installer Zenstruck Foundry** :
  ```bash
  docker compose exec php composer require zenstruck/foundry --dev
  ```
  *Outil pratique pour générer des données de test.*

- **Créer une factory** :
  ```bash
  docker compose exec php php bin/console make:factory
  ```
  *Génère une factory pour créer des instances d’entités.*

- **Installer les fixtures** :
  ```bash
  docker compose exec php composer require orm-fixtures --dev
  ```
  *Permet de gérer les jeux de données pour les tests.*