# 💊 Pharmacie Intelligente

Application web SaaS de gestion pharmaceutique développée avec **Laravel 11**, **Vue.js 3**, **MySQL** et **Tailwind CSS**.

---

# 📋 Présentation du Projet

**Pharmacie Intelligente** est une plateforme moderne permettant la gestion complète d'une pharmacie.

L'application facilite la gestion :

* des médicaments
* des stocks
* des ventes
* des clients
* des fournisseurs
* des commandes
* des utilisateurs
* des statistiques

Elle intègre également un système **multi-pharmacies** permettant à un **Super Administrateur** de gérer plusieurs pharmacies depuis une seule interface.

---

# 🎯 Objectifs du Projet

* Digitaliser la gestion quotidienne d'une pharmacie
* Réduire les erreurs liées aux stocks et aux ventes
* Automatiser les alertes de stock faible et de péremption
* Générer des rapports statistiques en temps réel
* Faciliter la gestion des clients et fournisseurs
* Assurer la traçabilité des opérations
* Permettre la gestion centralisée de plusieurs pharmacies

---

# 🚀 Fonctionnalités Principales

## 🔐 Authentification & Sécurité

* Authentification sécurisée avec Laravel Sanctum
* Gestion des sessions utilisateur
* Gestion des rôles et permissions
* Protection des routes API
* Contrôle d'accès selon le rôle

---

# 👥 Gestion des Rôles

| Rôle                 | Description                       |
| -------------------- | --------------------------------- |
| Super Administrateur | Gestion globale de la plateforme  |
| Administrateur       | Gestion complète d'une pharmacie  |
| Pharmacien           | Gestion des médicaments et stocks |
| Caissier             | Gestion des ventes                |

---

# 👑 Super Administrateur

Le Super Administrateur dispose d'une vue globale sur toutes les pharmacies.

## Fonctionnalités

### Dashboard Global

* Nombre total de pharmacies
* Nombre total d'utilisateurs
* Statistiques de ventes
* Graphiques analytiques

### Gestion des Pharmacies

* Création de pharmacies
* Modification
* Activation
* Suspension
* Suppression

### Gestion des Utilisateurs

* Consultation de tous les utilisateurs
* Gestion des rôles

### Sauvegardes

* Création de backups
* Téléchargement des sauvegardes
* Nettoyage des anciennes sauvegardes

---

# 👨‍💼 Administrateur de Pharmacie

L'administrateur gère uniquement sa pharmacie.

## Dashboard

* Chiffre d'affaires
* Nombre de ventes
* Panier moyen
* Top médicaments vendus

## Gestion des Médicaments

* Ajouter un médicament
* Modifier un médicament
* Supprimer un médicament
* Recherche avancée
* Import Excel
* Export Excel
* Soft Delete

## Gestion du Stock

* Entrées de stock
* Gestion par lots
* Alertes de stock faible
* Alertes de péremption
* Historique des mouvements

## Gestion des Ventes

* Création des ventes
* Consultation
* Annulation
* Génération PDF
* Envoi par email

## Gestion des Clients

* Création
* Modification
* Historique des achats
* Médicaments chroniques

## Gestion des Fournisseurs

* Ajouter
* Modifier
* Supprimer

## Gestion des Commandes

* Création de commandes
* Réception des commandes
* Mise à jour automatique du stock

## Rapports

* Statistiques détaillées
* Export Excel

## Gestion des Utilisateurs

* Création des comptes pharmaciens
* Création des comptes caissiers

---

# 💊 Pharmacien

## Fonctionnalités

### Médicaments

* Consultation
* Modification

### Stock

* Gestion des entrées
* Alertes
* Historique

### Ventes

* Création
* Consultation

### Clients

* Consultation
* Création

---

# 🏪 Caissier

## Fonctionnalités

### Ventes

* Création des ventes
* Consultation de ses ventes

### Clients

* Consultation

### Factures

* Téléchargement PDF

---

# 🛠 Technologies Utilisées

## Backend

| Technologie     | Version |
| --------------- | ------- |
| Laravel         | 12.x    |
| PHP             | 8.2+    |
| Laravel Sanctum | Latest  |
| MySQL           | 8.0+    |

## Frontend

| Technologie  | Version |
| ------------ | ------- |
| Vue.js       | 3.x     |
| Vite         | Latest  |
| Pinia        | Latest  |
| Vue Router   | 4.x     |
|      |

## Bibliothèques

| Bibliothèque   | Utilisation           |
| -------------- | --------------------- |
| Chart.js       | Graphiques            |
| DomPDF         | Factures PDF          |
| PhpSpreadsheet | Import / Export Excel |

---

# 🗄️ Base de Données

## Tables Principales

| Table           | Description           |
| --------------- | --------------------- |
| users           | Utilisateurs          |
| pharmacies      | Pharmacies            |
| categories      | Catégories            |
| medicaments     | Médicaments           |
| stock_lots      | Lots de stock         |
| clients         | Clients               |
| ventes          | Ventes                |
| ligne_ventes    | Détails des ventes    |
| fournisseurs    | Fournisseurs          |
| commandes       | Commandes             |
| ligne_commandes | Détails des commandes |

---

# 🔗 Relations Principales

```text
Pharmacie (1) ---- (N) Users

Pharmacie (1) ---- (N) Medicaments

Pharmacie (1) ---- (N) Clients

Pharmacie (1) ---- (N) Ventes

Categorie (1) ---- (N) Medicaments

Medicament (1) ---- (N) StockLots

Medicament (1) ---- (N) LigneVentes

Vente (1) ---- (N) LigneVentes

Client (1) ---- (N) Ventes

Commande (1) ---- (N) LigneCommandes

Fournisseur (1) ---- (N) Commandes
```

---

# 🚀 Installation

## Prérequis

* PHP 8.2+
* Composer
* Node.js 18+
* MySQL 8.0+

---

## 1. Cloner le projet

```bash
git clone https://github.com/Maurice-enkoura/pharmacie-intelligente-v2.git

cd pharmacie-intelligente
```

---

## 2. Installation du Backend

```bash
composer install

cp .env.example .env

php artisan key:generate
```

Configurer la base de données dans le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharmacie_db
DB_USERNAME=root
DB_PASSWORD=
```

Exécuter les migrations et seeders :

```bash
php artisan migrate --seed
```

Démarrer le serveur Laravel :

```bash
php artisan serve
```

---

## 3. Installation du Frontend

```bash
cd pharmacie-frontend

npm install

npm run dev
```

---

# 🌐 URLs du Projet

| Service     | URL                          |
| ----------- | ---------------------------- |
| Frontend    | http://localhost:5173        |
| Backend API | http://127.0.0.1:8000/api/v1 |

---

# 🔑 Comptes de Démonstration

| Rôle                 | Email                                                                       | Mot de passe |
| -------------------- | --------------------------------------------------------------------------- | ------------ |
| Super Administrateur | [superadmin@pharmacie.com](mailto:superadmin@pharmacie.com)                 | password     |
| Administrateur       | [admin@pharmacie-centrale.sn](mailto:admin@pharmacie-centrale.sn)           | password     |
| Pharmacien           | [pharmacien@pharmacie-centrale.sn](mailto:pharmacien@pharmacie-centrale.sn) | password     |
| Caissier             | [caissier@pharmacie-centrale.sn](mailto:caissier@pharmacie-centrale.sn)     | password     |

---

# 🔌 Principaux Endpoints API

## Authentification

| Méthode | Endpoint       |
| ------- | -------------- |
| POST    | /api/v1/login  |
| POST    | /api/v1/logout |
| GET     | /api/v1/user   |

---

## Médicaments

| Méthode | Endpoint                   |
| ------- | -------------------------- |
| GET     | /api/v1/medicaments        |
| POST    | /api/v1/medicaments        |
| PUT     | /api/v1/medicaments/{id}   |
| DELETE  | /api/v1/medicaments/{id}   |
| GET     | /api/v1/medicaments/export |
| POST    | /api/v1/medicaments/import |

---

## Ventes

| Méthode | Endpoint                       |
| ------- | ------------------------------ |
| GET     | /api/v1/ventes                 |
| POST    | /api/v1/ventes                 |
| GET     | /api/v1/ventes/{id}/pdf        |
| GET     | /api/v1/ventes/{id}/send-email |

---

## Super Administrateur

| Méthode | Endpoint                       |
| ------- | ------------------------------ |
| GET     | /api/v1/super-admin/stats      |
| GET     | /api/v1/super-admin/pharmacies |
| POST    | /api/v1/super-admin/pharmacies |
| GET     | /api/v1/super-admin/users      |
| GET     | /api/v1/backups                |
| POST    | /api/v1/backups/create         |

---

# 📬 Documentation API

La collection Postman est disponible dans :

```text
docs/
└── Pharmacie_API.postman_collection.json
```

---

# 📈 Évolutions Futures

* Notifications temps réel
* Gestion des ordonnances
* Paiement mobile (Wave, Orange Money)
* Application mobile Flutter
* Tableau de bord avancé avec IA
* Prévision automatique des ruptures de stock

---

# 👨‍💻 Auteur

**Maurice Aurfe Enkoura**

Étudiant en Licence 3 Génie Logiciel – ISI Dakar

Projet académique et professionnel de gestion pharmaceutique.
