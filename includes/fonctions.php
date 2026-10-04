<?php
// ============================================================
// Fonctions utilitaires
// ============================================================

function estConnecte() {
    return isset($_SESSION['patient_id']);
}

function estAdmin() {
    return isset($_SESSION['admin_id']);
}

function redirigerSiNonConnecte() {
    if (!estConnecte()) {
        header('Location: connexion.php');
        exit;
    }
}

function redirigerSiNonAdmin() {
    if (!estAdmin()) {
        header('Location: connexion_admin.php');
        exit;
    }
}

function nettoyer($chaine) {
    return htmlspecialchars(trim($chaine), ENT_QUOTES, 'UTF-8');
}

function formaterDate($date) {
    $mois = [1=>'janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $jours = [1=>'lundi',2=>'mardi',3=>'mercredi',4=>'jeudi',5=>'vendredi',6=>'samedi',7=>'dimanche'];
    $ts = strtotime($date);
    $jourSemaine = $jours[date('N', $ts)];
    return ucfirst($jourSemaine) . ' ' . date('j', $ts) . ' ' . $mois[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function libelleStatut($statut) {
    $libelles = [
        'en_attente' => '<span class="badge bg-warning text-dark">En attente</span>',
        'confirme'   => '<span class="badge bg-success">Confirmé</span>',
        'annule'     => '<span class="badge bg-danger">Annulé</span>',
        'termine'    => '<span class="badge bg-secondary">Terminé</span>',
    ];
    return $libelles[$statut] ?? $statut;
}

/**
 * Retourne les créneaux horaires disponibles pour un médecin à une date donnée
 * (à partir de ses disponibilités hebdomadaires, en retirant les créneaux déjà pris)
 */
function creneauxDisponibles(PDO $pdo, $medecinId, $date) {
    $jourSemaine = date('N', strtotime($date)); // 1 (lundi) à 7 (dimanche)

    $stmt = $pdo->prepare("SELECT heure_debut, heure_fin FROM disponibilites WHERE medecin_id = ? AND jour_semaine = ?");
    $stmt->execute([$medecinId, $jourSemaine]);
    $plages = $stmt->fetchAll();

    if (!$plages) return [];

    // Créneaux de 30 minutes
    $creneaux = [];
    foreach ($plages as $plage) {
        $debut = strtotime($plage['heure_debut']);
        $fin = strtotime($plage['heure_fin']);
        for ($t = $debut; $t < $fin; $t += 1800) {
            $creneaux[] = date('H:i:s', $t);
        }
    }

    // Retirer les créneaux déjà réservés (hors rendez-vous annulés)
    $stmt = $pdo->prepare("SELECT heure_rdv FROM rendez_vous WHERE medecin_id = ? AND date_rdv = ? AND statut != 'annule'");
    $stmt->execute([$medecinId, $date]);
    $pris = array_column($stmt->fetchAll(), 'heure_rdv');

    $disponibles = array_diff($creneaux, $pris);

    // Si c'est aujourd'hui, retirer les créneaux déjà passés
    if ($date === date('Y-m-d')) {
        $maintenant = date('H:i:s');
        $disponibles = array_filter($disponibles, fn($c) => $c > $maintenant);
    }

    sort($disponibles);
    return $disponibles;
}
