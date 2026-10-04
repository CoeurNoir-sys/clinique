# Plateforme de gestion des rendez-vous en ligne — Clinique Privée

Application web développée avec **HTML5, Bootstrap 5, PHP (PDO/MySQL), JavaScript**,
conçue pour fonctionner avec **XAMPP**.

## Fonctionnalités

**Côté patient**
- Inscription / connexion sécurisée (mots de passe hashés avec `password_hash`)
- Consultation de la liste des médecins par spécialité
- Prise de rendez-vous avec sélection dynamique des créneaux libres (AJAX, sans rechargement de page)
- Consultation et annulation de ses propres rendez-vous

**Côté administrateur**
- Connexion séparée (`/admin`)
- Tableau de bord avec statistiques (rendez-vous du jour, patients, médecins, en attente)
- Gestion complète des rendez-vous : filtrer, confirmer, annuler, marquer comme terminé
- Gestion des médecins : ajout, modification, suppression, définition des horaires de disponibilité

## Installation sous XAMPP

1. **Copier le projet**
   Placez le dossier `clinique/` dans `C:\xampp\htdocs\` (Windows) ou `/Applications/XAMPP/htdocs/` (Mac) ou `/opt/lampp/htdocs/` (Linux).

2. **Démarrer XAMPP**
   Lancez les modules **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.

3. **Créer la base de données**
   - Ouvrez `http://localhost/phpmyadmin`
   - Cliquez sur "Importer", sélectionnez le fichier `schema.sql` et validez.
   - Cela crée la base `clinique_rdv` avec les tables et des données de démonstration
     (4 médecins, 5 spécialités, 1 compte admin).

4. **Configurer la connexion (si besoin)**
   Le fichier `config.php` utilise par défaut :
   ```
   DB_HOST = localhost
   DB_NAME = clinique_rdv
   DB_USER = root
   DB_PASS = (vide)
   ```
   Adaptez ces valeurs si votre installation XAMPP utilise un autre mot de passe MySQL.

5. **Accéder au site**
   - Site patient : `http://localhost/clinique/`
   - Espace admin : `http://localhost/clinique/admin/connexion_admin.php`

## Compte administrateur par défaut

```
Identifiant : admin
Mot de passe : admin123
```

⚠️ Pensez à changer ce mot de passe en production (table `admins`).

## Structure du projet

```
clinique/
├── admin/
│   ├── connexion_admin.php
│   ├── dashboard.php
│   ├── medecins.php
│   ├── rendez-vous.php
│   └── deconnexion_admin.php
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── includes/
│   ├── header.php / footer.php           (pages patient)
│   ├── header_admin.php / footer_admin.php (pages admin)
│   └── fonctions.php                     (fonctions utilitaires)
├── config.php          (connexion BDD)
├── schema.sql          (structure + données de démo)
├── index.php            (accueil)
├── inscription.php
├── connexion.php
├── deconnexion.php
├── prendre-rdv.php
├── mes-rendez-vous.php
└── ajax_creneaux.php    (endpoint AJAX des créneaux disponibles)
```

## Sécurité mise en œuvre

- Requêtes préparées PDO partout (protection contre les injections SQL)
- Mots de passe hashés (`password_hash` / `password_verify`)
- Échappement systématique de l'affichage (`htmlspecialchars`)
- Vérification côté serveur de la disponibilité d'un créneau avant insertion (anti double-réservation)
- Sessions séparées pour l'espace patient et l'espace admin

## Pistes d'amélioration possibles

- Envoi d'e-mails de confirmation/rappel (via PHPMailer)
- Upload de photo de profil pour les médecins
- Gestion de plusieurs cliniques / plusieurs administrateurs avec rôles
- Export des rendez-vous en PDF/Excel
