<?php
require 'config.php';
require 'includes/fonctions.php';
$titrePage = 'Accueil';

$medecins = $pdo->query("
    SELECT m.*, s.nom AS specialite
    FROM medecins m
    LEFT JOIN specialites s ON m.specialite_id = s.id
    ORDER BY s.nom, m.nom
")->fetchAll();

require 'includes/header.php';
?>

<section class="hero text-center">
    <div class="container">
        <h1><i class="fa-solid fa-heart-pulse"></i> Prenez rendez-vous en quelques clics</h1>
        <p>Choisissez votre médecin, sélectionnez un créneau disponible et confirmez votre consultation.</p>
        <a href="<?= estConnecte() ? 'prendre-rdv.php' : 'connexion.php' ?>" class="btn btn-accent btn-lg mt-3">
            <i class="fa-solid fa-calendar-check"></i> Prendre rendez-vous
        </a>
    </div>
</section>

<section class="container my-5">
    <h2 class="mb-4 text-center" style="color:var(--bleu-fonce)">Notre équipe médicale</h2>
    <div class="row g-4">
        <?php foreach ($medecins as $m): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card card-medecin p-3 text-center">
                    <div class="avatar mx-auto mb-3">
                        <?= strtoupper(substr($m['prenom'],0,1) . substr($m['nom'],0,1)) ?>
                    </div>
                    <h5 class="mb-1">Dr <?= nettoyer($m['prenom'] . ' ' . $m['nom']) ?></h5>
                    <p class="text-muted mb-2"><?= nettoyer($m['specialite'] ?? 'Généraliste') ?></p>
                    <a href="<?= estConnecte() ? 'prendre-rdv.php?medecin_id=' . $m['id'] : 'connexion.php' ?>" class="btn btn-sm btn-clinique">Prendre RDV</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
