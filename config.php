<?php
// ============================================================
// Configuration & connexion à la base de données
// ============================================================
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'clinique_rdv');
define('DB_USER', 'root');
define('DB_PASS', ''); // par défaut, XAMPP n'a pas de mot de passe root

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

define('NOM_CLINIQUE', 'Clinique Privée Sainte-Marie');
