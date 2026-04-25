# IdentiGuinée - Plateforme Nationale d'Identité Numérique

## Présentation

IdentiGuinée est une plateforme numérique complète pour la gestion des documents d'identité en République de Guinée. Développée dans le cadre du MIABE Hackathon 2026, cette solution vise à digitaliser et simplifier les processus administratifs liés à l'identité citoyenne.

## Objectifs

- **Lutter contre la corruption** : Éliminer les intermédiaires et les pots-de-vin
- **Réduire les délais** : Passer de plusieurs mois à quelques jours
- **Assurer la transparence** : Suivi en temps réel des demandes
- **Garantir la sécurité** : Protection des données personnelles
- **Faciliter l'accès** : Services disponibles 24/7

## Fonctionnalités

### 🏠 Site Vitrine
- Page d'accueil moderne et professionnelle
- Présentation complète du projet
- Sections : problème, solution, fonctionnalités, processus
- Design responsive avec couleurs institutionnelles (bleu, blanc, orange)

### 👤 Espace Citoyen
- Inscription et connexion sécurisées
- Tableau de bord personnel
- Formulaire de demande de documents (CNI, Passeport, Permis)
- Suivi des demandes en temps réel
- Téléchargement des documents validés

### 🏛️ Espace Administration
- Connexion administrateur sécurisée
- Tableau de bord avec statistiques en temps réel
- Gestion complète des demandes
- Validation/rejet avec motifs
- Historique des traitements

### 🔍 Espace Vérification
- Interface publique de vérification
- Vérification par code ou QR code
- Résultats instantanés
- Base de données officielle

## Architecture Technique

### Backend
- **Framework** : Laravel 10
- **Architecture** : MVC
- **Authentification** : Session-based
- **Base de données** : MySQL (configurable)

### Frontend
- **Templates** : Blade
- **CSS Framework** : Bootstrap 5
- **Icons** : Font Awesome 6
- **Design** : Responsive et moderne

### Sécurité
- Protection CSRF
- Validation des entrées
- Sessions sécurisées
- Middleware d'authentification

## Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Serveur web (Apache/Nginx)

### Étapes

1. **Cloner le projet**
   ```bash
   git clone <repository-url>
   cd identiguinee
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurer la base de données**
   ```bash
   php artisan migrate
   ```

5. **Démarrer le serveur**
   ```bash
   php artisan serve
   ```

## Accès de démonstration

### Espace Citoyen
- **Email** : citoyen@identiguinee.gn
- **Mot de passe** : password

### Administration
- **Email** : admin@identiguinee.gn
- **Mot de passe** : admin123

### Codes de test pour vérification
- CNI-2024-001234 (Carte d'identité valide)
- PAS-2024-000567 (Passeport valide)
- PER-2024-000890 (Permis valide)

## Structure du projet

```
identiguinee/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── CitizenController.php
│   │   │   ├── AdminController.php
│   │   │   ├── VerifierController.php
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   │       ├── CitizenAuth.php
│   │       └── AdminAuth.php
│   └── ...
├── resources/
│   └── views/
│       ├── layouts/
│       ├── partials/
│       ├── auth/
│       ├── citizen/
│       ├── admin/
│       ├── verifier/
│       └── home.blade.php
├── routes/
│   └── web.php
├── config/
├── composer.json
└── README.md
```

## Fonctionnalités détaillées

### Gestion des demandes
- Création de demandes avec formulaire complet
- Upload de documents (simulation)
- Statuts : En cours, Validée, Rejetée
- Notifications automatiques

### Tableaux de bord
- Statistiques en temps réel
- Graphiques et indicateurs
- Filtres et recherche
- Export de données

### Vérification
- Validation instantanée
- Affichage des informations du document
- Historique des vérifications
- Protection contre la fraude

## Technologies utilisées

- **PHP 8.1+** : Langage principal
- **Laravel 10** : Framework backend
- **MySQL** : Base de données
- **Bootstrap 5** : Framework CSS
- **Font Awesome 6** : Icônes
- **Blade** : Moteur de templates
- **jQuery** : Interactivité JavaScript

## Contribuer

Ce projet a été développé pour le MIABE Hackathon 2026. Les contributions sont les bienvenues pour améliorer la plateforme.

## Licence

Projet développé dans le cadre d'une initiative gouvernementale pour la digitalisation des services publics en Guinée.

## Contact

- **Email** : contact@identiguinee.gn
- **Téléphone** : +224 622 12 34 56
- **Adresse** : Conakry, Guinée

---

© 2024 République de Guinée - Ministère de l'Intérieur
