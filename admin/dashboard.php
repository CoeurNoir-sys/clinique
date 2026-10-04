<?php
require '../config.php';
require '../includes/fonctions.php';
redirigerSiNonAdmin();
$titrePage = 'Tableau de bord';

$totalRdv = $pdo->query("SELECT COUNT(*) FROM rendez_vous")->fetchColumn();
$rdvAujourdhui = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE date_rdv = ? AND statut != 'annule'");
$rdvAujourdhui->execute([date('Y-m-d')]);
$rdvAujourdhui = $rdvAujourdhui->fetchColumn();
$totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$totalMedecins = $pdo->query("SELECT COUNT(*) FROM medecins")->fetchColumn();
$enAttente = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'en_attente'")->fetchColumn();

$prochains = $pdo->query("
    SELECT rv.*, p.nom AS patient_nom, p.prenom AS patient_prenom, m.nom AS medecin_nom, m.prenom AS medecin_prenom
    FROM rendez_vous rv
    JOIN patients p ON rv.patient_id = p.id
    JOIN medecins m ON rv.medecin_id = m.id
    WHERE rv.date_rdv >= CURDATE() AND rv.statut != 'annule'
    ORDER BY rv.date_rdv, rv.heure_rdv
    LIMIT 8
")->fetchAll();

require '../includes/header_admin.php';
?>

<h3 class="mb-4" style="color:var(--bleu-fonce)">Tableau de bord</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 text-center card-medecin">
            <i class="fa-solid fa-calendar-days fa-2x mb-2" style="color:var(--bleu-clinique)"></i>
            <h3><?= $totalRdv ?></h3>
            <p class="text-muted mb-0">Rendez-vous au total</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center card-medecin">
            <i class="fa-solid fa-clock fa-2x mb-2" style="color:var(--accent)"></i>
            <h3><?= $rdvAujourdhui ?></h3>
            <p class="text-muted mb-0">Rendez-vous aujourd'hui</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center card-medecin">
            <i class="fa-solid fa-users fa-2x mb-2" style="color:var(--bleu-clinique)"></i>
            <h3><?= $totalPatients ?></h3>
            <p class="text-muted mb-0">Patients inscrits</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center card-medecin">
            <i class="fa-solid fa-user-doctor fa-2x mb-2" style="color:var(--bleu-clinique)"></i>
            <h3><?= $totalMedecins ?></h3>
            <p class="text-muted mb-0">Médecins</p>
        </div>
    </div>
</div>

<?php if ($enAttente > 0): ?>
    <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation"></i> <?= $enAttente ?> rendez-vous en attente de confirmation. <a href="rendez-vous.php?statut=en_attente">Voir</a></div>
<?php endif; ?>

<h5 class="mt-4 mb-3">Prochains rendez-vous</h5>
<div class="table-responsive">
    <table class="table table-clinique bg-white align-middle">
        <thead>
            <tr><th>Date</th><th>Heure</th><th>Patient</th><th>Médecin</th><th>Statut</th></tr>
        </thead>
        <tbody>
            <?php foreach ($prochains as $rdv): ?>
                <tr>
                    <td><?= formaterDate($rdv['date_rdv']) ?></td>
                    <td><?= substr($rdv['heure_rdv'], 0, 5) ?></td>
                    <td><?= nettoyer($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></td>
                    <td>Dr <?= nettoyer($rdv['medecin_prenom'] . ' ' . $rdv['medecin_nom']) ?></td>
                    <td><?= libelleStatut($rdv['statut']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$prochains): ?>
                <tr><td colspan="5" class="text-center text-muted">Aucun rendez-vous à venir.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require '../includes/footer_admin.php'; ?>
