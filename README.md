# 💊 Pharmacie Intelligente - Application de Gestion Pharmaceutique

## 📋 Présentation du Projet

**Pharmacie Intelligente** est une application web complète conçue pour la gestion d'une pharmacie. Elle permet aux administrateurs, pharmaciens, caissiers et super administrateurs de gérer efficacement les médicaments, les ventes, les stocks, les clients, les fournisseurs et les rapports statistiques à partir d'une interface intuitive et sécurisée.

---

## 🎯 Objectifs du Projet

- Digitaliser la gestion quotidienne d'une pharmacie
- Réduire les erreurs liées aux stocks et aux ventes
- Automatiser le suivi des médicaments et des alertes
- Générer des rapports de gestion en temps réel
- Faciliter la gestion des clients et fournisseurs
- Assurer une meilleure traçabilité des opérations
- Permettre au Super Admin de gérer l'ensemble des pharmacies

---

## 🚀 Fonctionnalités Principales

### 🔐 Authentification et Sécurité

- Connexion sécurisée avec Laravel Sanctum
- Gestion des sessions utilisateur
- Gestion des rôles et permissions (Middleware + Policies)
- Protection des routes API

### 👥 Rôles disponibles

| Rôle | Email | Mot de passe | Permissions |
|------|-------|--------------|--------------|
| **Super Administrateur** | superadmin@pharmacie.com | password | Gestion globale : pharmacies, utilisateurs, backups, statistiques |
| **Administrateur** | admin@pharmacie-centrale.sn | password | Gestion complète de SA pharmacie |
| **Pharmacien** | pharmacien@pharmacie-centrale.sn | password | Gestion médicaments, ventes, stocks |
| **Caissier** | caissier@pharmacie-centrale.sn | password | Gestion des ventes uniquement |

---

### 👑 Super Administrateur

Le Super Admin a une vision globale de la plateforme et peut :

- ✅ Gérer toutes les pharmacies (création, modification, activation, suspension)
- ✅ Voir les statistiques globales (chiffre d'affaires total, nombre de pharmacies, nombre d'utilisateurs)
- ✅ Gérer les utilisateurs de toutes les pharmacies
- ✅ Effectuer des backups de la base de données
- ✅ Renouveler les abonnements des pharmacies

**Routes dédiées au Super Admin :**

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/v1/super-admin/stats` | Statistiques globales |
| GET | `/api/v1/super-admin/pharmacies` | Lister toutes les pharmacies |
| POST | `/api/v1/super-admin/pharmacies` | Créer une pharmacie |
| PUT | `/api/v1/super-admin/pharmacies/{id}` | Modifier une pharmacie |
| DELETE | `/api/v1/super-admin/pharmacies/{id}` | Supprimer une pharmacie |
| POST | `/api/v1/super-admin/pharmacies/{id}/activate` | Activer une pharmacie |
| POST | `/api/v1/super-admin/pharmacies/{id}/suspend` | Suspendre une pharmacie |
| GET | `/api/v1/super-admin/users` | Lister tous les utilisateurs |
| GET | `/api/v1/backups` | Lister les backups |
| POST | `/api/v1/backups/create` | Créer un backup |
| GET | `/api/v1/backups/download/{filename}` | Télécharger un backup |

---

### 💊 Gestion des Médicaments

- ✅ Ajouter, modifier, supprimer un médicament
- ✅ Archiver un médicament (Soft Delete)
- ✅ Recherche dynamique par nom, DCI, catégorie
- ✅ Filtrage avancé (catégorie, ordonnance requise)
- ✅ Pagination
- ✅ Import Excel (XLSX, XLS)
- ✅ Export Excel

**Informations gérées :**
- Nom commercial, DCI, forme, dosage
- Catégorie
- Prix d'achat, prix de vente
- Stock actuel, seuil d'alerte
- Ordonnance obligatoire ou non

---

### 📦 Gestion des Stocks

**Gestion par lots :**
- Quantité, numéro de lot
- Date d'entrée, date de péremption
- Fournisseur associé

**Alertes automatiques :**
- Stock bas (< seuil d'alerte)
- Péremption proche (< 30 jours)

**Historique :**
- Entrées de stock
- Sorties de stock (ventes)

---

### 💰 Gestion des Ventes

**Types de vente :**
- Vente sans ordonnance
- Vente avec ordonnance (avec référence automatique)

**Fonctionnalités :**
- Panier d'achat dynamique
- Ajout de plusieurs produits
- Calcul automatique du total
- Calcul automatique de la monnaie
- Historique des ventes

**Facturation :**
- Génération PDF (DomPDF)
- Téléchargement PDF
- Envoi par email (Mailtrap / SMTP)

---

### 👥 Gestion des Clients

- Création, modification, suppression client
- Historique d'achats
- Recherche rapide par nom, prénom, téléphone
- Médicaments chroniques (rappel automatique)

**Informations :**
- Nom, prénom
- Téléphone, email
- Adresse
- Médicaments chroniques

---

### 🏭 Gestion des Fournisseurs

- Ajouter, modifier, supprimer un fournisseur
- Consulter les commandes associées

**Informations :**
- Nom, contact
- Téléphone, email
- Adresse

---

### 📋 Gestion des Commandes

- Création de bon de commande
- Réception de commande
- Mise à jour automatique du stock
- Historique des commandes

---

### 📊 Tableau de Bord (Dashboard)

Visualisation en temps réel avec graphiques (Chart.js) :
- Chiffre d'affaires (journalier, mensuel, annuel)
- Nombre de ventes
- Panier moyen
- Clients actifs
- Top médicaments vendus
- Répartition des modes de paiement
- Performance des caissiers
- Alertes stock (stock bas + péremption)

---

### 📧 Gestion des Emails

- Envoi automatique des factures par email
- Support SMTP
- Configuration Mailtrap pour les tests

---

### 📂 Import / Export Excel

**Import :**
- Format supporté : XLSX, XLS
- Import des médicaments depuis Excel
- Gestion des doublons (vérification nom + DCI)

**Export :**
- Export des médicaments
- Export des rapports statistiques

---

## 🛠 Technologies Utilisées

### Backend

| Technologie | Version |
|-------------|---------|
| Laravel | 11.x |
| PHP | 8.2+ |
| Laravel Sanctum | - |
| Eloquent ORM | - |
| MySQL | 8.0+ |

### Frontend

| Technologie | Version |
|-------------|---------|
| Vue.js | 3.x |
| Vite | - |
| Pinia | - |
| Vue Router | 4.x |
| Axios | - |
| Tailwind CSS | 3.x |

### Bibliothèques

| Bibliothèque | Utilisation |
|--------------|-------------|
| Chart.js | Graphiques dashboard |
| DomPDF | Génération factures PDF |
| PhpSpreadsheet | Import/Export Excel |

---

## 🏗 Architecture Technique

### Backend (Laravel API REST)


---

## 📁 Structure du Projet

### Backend

```text
app/
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── AuthController.php
│   │   ├── MedicamentController.php
│   │   ├── VenteController.php
│   │   ├── ClientController.php
│   │   ├── FournisseurController.php
│   │   ├── CommandeController.php
│   │   ├── StockController.php
│   │   ├── DashboardController.php
│   │   ├── RapportController.php
│   │   ├── UserController.php
│   │   ├── PharmacieController.php
│   │   └── SuperAdminController.php
│   └── Middleware/
│       └── RoleMiddleware.php
│
├── Models/
│   ├── User.php
│   ├── Medicament.php
│   ├── Vente.php
│   ├── Client.php
│   ├── Fournisseur.php
│   ├── Commande.php
│   ├── StockLot.php
│   ├── Categorie.php
│   └── Pharmacie.php
│
├── Observers/
│   ├── VenteObserver.php
│   └── StockLotObserver.php
│
└── Policies/
    ├── MedicamentPolicy.php
    └── VentePolicy.php

database/
├── migrations/ (12 migrations)
└── seeders/ (8 seeders)

routes/
└── api.php
```

### Frontend

```text
src/
├── assets/
├── components/
├── layouts/
├── router/
├── services/
├── stores/
├── views/
│   ├── auth/
│   ├── admin/
│   ├── pharmacien/
│   ├── caissier/
│   ├── super-admin/
│   └── shared/
├── App.vue
└── main.js
```



---

## 🚀 Installation

### Prérequis

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### 1️⃣ Cloner le Projet

```bash
git clone https://github.com/MauriceEnkoura/pharmacie-intelligente.git
cd pharmacie-intelligente

composer install
cp .env.example .env
php artisan key:generate



## ⚙️ Installation

### 2️⃣ Installation Backend

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

Lancer les migrations et les seeders :

```bash
php artisan migrate --seed
```

Démarrer le serveur Laravel :

```bash
php artisan serve
```

---

### 3️⃣ Installation Frontend

```bash
cd pharmacie-frontend

npm install

npm run dev
```

---

## 🌐 URLs du Projet

| Service | URL |
|----------|------|
| Frontend | http://localhost:5173 |
| Backend API | http://127.0.0.1:8000/api/v1 |

---

##  Base de Données

### Tables (11 tables)

| Table | Description |
|---------|-------------|
| users | Utilisateurs (avec rôle et pharmacy_id) |
| pharmacies | Pharmacies (gestion multi-pharmacies) |
| categories | Catégories de médicaments |
| medicaments | Catalogue des médicaments |
| stock_lots | Lots de stock |
| clients | Clients |
| ventes | Transactions de vente |
| ligne_ventes | Détails des ventes |
| fournisseurs | Fournisseurs |
| commandes | Commandes fournisseurs |
| ligne_commandes | Détails des commandes |

### Relations

```text
Pharmacie (1) → User (n)
Pharmacie (1) → Medicament (n)
Pharmacie (1) → Vente (n)
Pharmacie (1) → Client (n)

Categorie (1) → Medicament (n)

Medicament (1) → StockLot (n)

Medicament (1) → LigneVente (n)
LigneVente (n) → Vente (1)
Vente (n) → Client (1)

Vente (n) → User (1)

Medicament (1) → LigneCommande (n)
LigneCommande (n) → Commande (1)
Commande (n) → Fournisseur (1)
```

---

##  Seeders Disponibles

| Seeder | Données générées |
|----------|----------------|
| RoleSeeder | 4 utilisateurs (super_admin, admin, pharmacien, caissier) |
| PharmacieSeeder | 3 pharmacies |
| CategorieSeeder | 7 catégories |
| FournisseurSeeder | 4 fournisseurs |
| MedicamentSeeder | 7 médicaments avec lots de stock |
| ClientSeeder | 5 clients |
| VenteSeeder | 4 ventes |
| CommandeSeeder | 4 commandes |

### Exécution

```bash
php artisan db:seed
```

---

## 📬 Documentation API

La collection Postman est disponible dans le dossier :

```text
docs/
└── Pharmacie_API.postman_collection.json
```