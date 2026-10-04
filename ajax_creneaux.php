<?php
require 'config.php';
require 'includes/fonctions.php';
header('Content-Type: application/json');

$medecinId = (int)($_GET['medecin_id'] ?? 0);
$date = $_GET['date'] ?? '';

if (!$medecinId || !$date || $date < date('Y-m-d')) {
    echo json_encode(['creneaux' => []]);
    exit;
}

$creneaux = creneauxDisponibles($pdo, $medecinId, $date);
echo json_encode(['creneaux' => array_values($creneaux)]);
