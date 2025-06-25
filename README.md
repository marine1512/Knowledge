# Connexion
Nom d'utilisateur : admin   
Mot de passe : admin

# Projet "Knowledge Learning" avec Symfony et Stripe

Un projet de plateforme de commerce électronique développé avec **Symfony** pour la gestion et **Stripe** pour les paiements en ligne (Checkout).

## Description

Ce projet est une application de commerce électronique créée avec Symfony 6. Elle permet aux utilisateurs de :
- Ajouter des produits à un panier
- Finaliser leurs paiements grâce à **Stripe Checkout**
- Consulter des détails de produit
- Gérer les tailles et stocks via votre base de données.

---

## Technologies utilisées

Voici les technologies et services utilisés pour ce projet :

- **Symfony 6** : Framework PHP pour les applications web
- **Stripe** : Gestion des paiements en ligne
- **Doctrine ORM** : Gestion de la base de données
- **Twig** : Moteur de templates pour l'affichage HTML
- **Webpack Encore** : Gestion des assets front-end (CSS, JS)

---

## Prérequis

Avant d'installer le projet, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- PHP 8.2 ou supérieur
- Composer
- Symfony CLI
- Serveur SQL (MySQL, SQLite, PostgreSQL, …)
- Node.js et npm (ou yarn) pour les assets front-end

---

## Installation

Suivez ces étapes pour installer et lancer le projet localement :

### 1. Clonez le dépôt

Cloner ce projet depuis GitHub :
```bash
git clone https://github.com/marine1512/knowledge.git
cd symfonymm
```

### 2. Installez les dépendances
Installez les dépendances PHP avec Composer :
```bash
composer install
```

Installez les dépendances front-end :
```bash
npm install
npm run dev
```

### 3. Configurez le fichier `.env`

Dupliquez le fichier `.env` en `.env.local` :
```bash
cp .env .env.local
```
Ensuite, configurez les paramètres de votre base de données et ajoutez votre clé API Stripe dans le fichier `.env.local` :
```env
### Exemple de config .env.local ###
DATABASE_URL="mysql://user:password@127.0.0.1:3306/nom_bdd"
STRIPE_SECRET_KEY="sk_test_votre_cle_stripe"
```

### 4. Configurez la base de données

Exécutez les migrations pour créer les tables nécessaires dans votre base de données :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Lancez l'application

Lancez le serveur Symfony avec :
```bash
symfony serve
```

Votre application sera disponible à l’adresse [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Utilisation

Voici comment utiliser votre application :

1. Ajoutez des produits dans votre base de données via les fixtures ou un outil d'administration.
2. Parcourez les produits et ajoutez-les à votre panier.
3. Finalisez l'achat en procédant au paiement Stripe.

---

## Fonctionnalités

- **Panier :** Ajoutez, modifiez et supprimez des produits dans un panier.
- **Paiements Stripe :** Intégration complète avec Stripe Checkout pour gérer les paiements.
- **Catalogue de produits :** Affichage des produits disponibles avec images, tailles et prix.
- **Gestion des stocks :** Les tailles disponibles sont gérées via des données JSON stockées dans la base de données.

---

## Tests unitaires

### Écrire et exécuter les tests

Ce projet utilise PHPUnit pour les tests unitaires.

1. Assurez-vous que les dépendances sont installées :
   ```bash
   composer install
   ```

2. Lancer tous les tests :
   ```bash
   ./vendor/bin/phpunit
   ```

3. Lancer des tests spécifiques, par exemple les tests pour l'entité `User` :
   ```bash
   ./vendor/bin/phpunit tests/Entity/UserTest.php
   ```
