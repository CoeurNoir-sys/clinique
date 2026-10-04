<?php
require 'config.php';
require 'includes/fonctions.php';
redirigerSiNonConnecte();
$titrePage = 'Mes rendez-vous';

// Annulation d'un rendez-vous
if (isset($_GET['annuler'])) {
    $rdvId = (int)$_GET['annuler'];
    $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = 'annule' WHERE id = ? AND patient_id = ?");
    $stmt->execute([$rdvId, $_SESSION['patient_id']]);
    header('Location: mes-rendez-vous.php?annule=1');
    exit;
}

$stmt = $pdo->prepare("
    SELECT rv.*, m.nom AS medecin_nom, m.prenom AS medecin_prenom, s.nom AS specialite
    FROM rendez_vous rv
    JOIN medecins m ON rv.medecin_id = m.id
    LEFT JOIN specialites s ON m.specialite_id = s.id
    WHERE rv.patient_id = ?
    ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC
");
$stmt->execute([$_SESSION['patient_id']]);
$rendezVous = $stmt->fetchAll();

require 'includes/header.php';
?>

<div class="container my-5">
    <h3 class="mb-4" style="color:var(--bleu-fonce)"><i class="fa-solid fa-list-check"></i> Mes rendez-vous</h3>

    <?php if (isset($_GET['succes'])): ?>
        <div class="alert alert-success">Votre rendez-vous a bien été enregistré. Il est en attente de confirmation.</div>
    <?php endif; ?>
    <?php if (isset($_GET['annule'])): ?>
        <div class="alert alert-info">Le rendez-vous a été annulé.</div>
    <?php endif; ?>

    <?php if (!$rendezVous): ?>
        <p class="text-muted">Vous n'avez encore aucun rendez-vous. <a href="prendre-rdv.php">Prenez-en un maintenant</a>.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-clinique bg-white align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Médecin</th>
                        <th>Spécialité</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rendezVous as $rdv): ?>
                        <tr>
                            <td><?= formaterDate($rdv['date_rdv']) ?></td>
                            <td><?= substr($rdv['heure_rdv'], 0, 5) ?></td>
                            <td>Dr <?= nettoyer($rdv['medecin_prenom'] . ' ' . $rdv['medecin_nom']) ?></td>
                            <td><?= nettoyer($rdv['specialite'] ?? '-') ?></td>
                            <td><?= nettoyer($rdv['motif'] ?: '-') ?></td>
                            <td><?= libelleStatut($rdv['statut']) ?></td>
                            <td>
                                <?php if (in_array($rdv['statut'], ['en_attente','confirme']) && $rdv['date_rdv'] >= date('Y-m-d')): ?>
                                    <a href="mes-rendez-vous.php?annuler=<?= $rdv['id'] ?>" class="btn btn-sm btn-outline-danger btn-annuler-rdv">Annuler</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
