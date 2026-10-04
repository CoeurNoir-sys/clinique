<?php
require '../config.php';
require '../includes/fonctions.php';
redirigerSiNonAdmin();
$titrePage = 'Rendez-vous';

// Changement de statut
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $statutsValides = ['confirme', 'annule', 'termine', 'en_attente'];
    if (in_array($action, $statutsValides)) {
        $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = ? WHERE id = ?");
        $stmt->execute([$action, $id]);
    }
    header('Location: rendez-vous.php' . (isset($_GET['statut']) ? '?statut=' . urlencode($_GET['statut']) : ''));
    exit;
}

$filtreStatut = $_GET['statut'] ?? '';

$sql = "
    SELECT rv.*, p.nom AS patient_nom, p.prenom AS patient_prenom, p.telephone AS patient_tel,
           m.nom AS medecin_nom, m.prenom AS medecin_prenom
    FROM rendez_vous rv
    JOIN patients p ON rv.patient_id = p.id
    JOIN medecins m ON rv.medecin_id = m.id
";
$params = [];
if ($filtreStatut) {
    $sql .= " WHERE rv.statut = ?";
    $params[] = $filtreStatut;
}
$sql .= " ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rendezVous = $stmt->fetchAll();

require '../includes/header_admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 style="color:var(--bleu-fonce)">Gestion des rendez-vous</h3>
    <div class="btn-group">
        <a href="rendez-vous.php" class="btn btn-sm btn-outline-secondary <?= !$filtreStatut ? 'active' : '' ?>">Tous</a>
        <a href="rendez-vous.php?statut=en_attente" class="btn btn-sm btn-outline-warning <?= $filtreStatut==='en_attente' ? 'active' : '' ?>">En attente</a>
        <a href="rendez-vous.php?statut=confirme" class="btn btn-sm btn-outline-success <?= $filtreStatut==='confirme' ? 'active' : '' ?>">Confirmés</a>
        <a href="rendez-vous.php?statut=annule" class="btn btn-sm btn-outline-danger <?= $filtreStatut==='annule' ? 'active' : '' ?>">Annulés</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-clinique bg-white align-middle">
        <thead>
            <tr>
                <th>Date</th><th>Heure</th><th>Patient</th><th>Téléphone</th><th>Médecin</th><th>Motif</th><th>Statut</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rendezVous as $rdv): ?>
                <tr>
                    <td><?= formaterDate($rdv['date_rdv']) ?></td>
                    <td><?= substr($rdv['heure_rdv'], 0, 5) ?></td>
                    <td><?= nettoyer($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></td>
                    <td><?= nettoyer($rdv['patient_tel']) ?></td>
                    <td>Dr <?= nettoyer($rdv['medecin_prenom'] . ' ' . $rdv['medecin_nom']) ?></td>
                    <td><?= nettoyer($rdv['motif'] ?: '-') ?></td>
                    <td><?= libelleStatut($rdv['statut']) ?></td>
                    <td class="text-nowrap">
                        <?php if ($rdv['statut'] === 'en_attente'): ?>
                            <a href="?action=confirme&id=<?= $rdv['id'] ?>&statut=<?= $filtreStatut ?>" class="btn btn-sm btn-success" title="Confirmer"><i class="fa-solid fa-check"></i></a>
                        <?php endif; ?>
                        <?php if ($rdv['statut'] !== 'annule'): ?>
                            <a href="?action=annule&id=<?= $rdv['id'] ?>&statut=<?= $filtreStatut ?>" class="btn btn-sm btn-danger" title="Annuler" onclick="return confirm('Annuler ce rendez-vous ?')"><i class="fa-solid fa-xmark"></i></a>
                        <?php endif; ?>
                        <?php if ($rdv['statut'] === 'confirme'): ?>
                            <a href="?action=termine&id=<?= $rdv['id'] ?>&statut=<?= $filtreStatut ?>" class="btn btn-sm btn-secondary" title="Marquer terminé"><i class="fa-solid fa-flag-checkered"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rendezVous): ?>
                <tr><td colspan="8" class="text-center text-muted">Aucun rendez-vous trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require '../includes/footer_admin.php'; ?>
