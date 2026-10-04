-- ============================================================
-- Plateforme de gestion des rendez-vous - Clinique Privée
-- Base de données MySQL (à importer via phpMyAdmin sous XAMPP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS clinique_rdv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinique_rdv;

-- Table des patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des spécialités
CREATE TABLE specialites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Table des médecins
CREATE TABLE medecins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    specialite_id INT,
    email VARCHAR(150),
    telephone VARCHAR(20),
    photo VARCHAR(255) DEFAULT 'assets/img/medecin-default.png',
    FOREIGN KEY (specialite_id) REFERENCES specialites(id) ON DELETE SET NULL
);

-- Table des créneaux disponibles (horaires de travail par médecin)
CREATE TABLE disponibilites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medecin_id INT NOT NULL,
    jour_semaine TINYINT NOT NULL COMMENT '1=Lundi ... 7=Dimanche',
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE
);

-- Table des rendez-vous
CREATE TABLE rendez_vous (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT NOT NULL,
    date_rdv DATE NOT NULL,
    heure_rdv TIME NOT NULL,
    motif VARCHAR(255),
    statut ENUM('en_attente','confirme','annule','termine') DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE
);

-- Table des administrateurs
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- ============================================================
-- Données de démonstration
-- ============================================================

INSERT INTO specialites (nom) VALUES
('Médecine générale'),
('Cardiologie'),
('Dermatologie'),
('Pédiatrie'),
('Gynécologie');

INSERT INTO medecins (nom, prenom, specialite_id, email, telephone) VALUES
('Diop', 'Fatou', 1, 'f.diop@clinique.sn', '77 123 45 67'),
('Ndiaye', 'Moussa', 2, 'm.ndiaye@clinique.sn', '77 234 56 78'),
('Sarr', 'Awa', 3, 'a.sarr@clinique.sn', '77 345 67 89'),
('Fall', 'Ibrahima', 4, 'i.fall@clinique.sn', '77 456 78 90');

-- Disponibilités : Lundi à Vendredi, 8h-16h pour chaque médecin
INSERT INTO disponibilites (medecin_id, jour_semaine, heure_debut, heure_fin)
SELECT m.id, j.jour, '08:00:00', '16:00:00'
FROM medecins m
JOIN (SELECT 1 AS jour UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) j;

-- Compte admin par défaut : identifiant "admin" / mot de passe "admin123"
-- (le hash correspond à password_hash('admin123', PASSWORD_DEFAULT))
INSERT INTO admins (nom_utilisateur, mot_de_passe) VALUES
('admin', '$2b$12$YZpMwADlEbPIpwCxzaYfue5k8CqELyYc4QmAqLq52GxJ/PZXqCtxe');
