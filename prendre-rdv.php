<?php
require 'config.php';
require 'includes/fonctions.php';
redirigerSiNonConnecte();
$titrePage = 'Prendre rendez-vous';

$erreur = '';
$succes = '';

$medecins = $pdo->query("
    SELECT m.*, s.nom AS specialite
    FROM medecins m
    LEFT JOIN specialites s ON m.specialite_id = s.id
    ORDER BY s.nom, m.nom
")->fetchAll();

$medecinPreselectionne = (int)($_GET['medecin_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medecinId = (int)($_POST['medecin_id'] ?? 0);
    $date = $_POST['date_rdv'] ?? '';
    $heure = $_POST['heure_rdv'] ?? '';
    $motif = nettoyer($_POST['motif'] ?? '');

    if (!$medecinId || !$date || !$heure) {
        $erreur = "Veuillez choisir un médecin, une date et un créneau horaire.";
    } else {
        // Vérifier que le créneau est toujours disponible (évite les doubles réservations)
        $disponibles = creneauxDisponibles($pdo, $medecinId, $date);
        if (!in_array($heure, $disponibles)) {
            $erreur = "Ce créneau vient d'être réservé par quelqu'un d'autre. Merci d'en choisir un autre.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO rendez_vous (patient_id, medecin_id, date_rdv, heure_rdv, motif) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['patient_id'], $medecinId, $date, $heure, $motif]);
            header('Location: mes-rendez-vous.php?succes=1');
            exit;
        }
    }
}

require 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-form p-4">
                <h3 class="text-center mb-4" style="color:var(--bleu-fonce)"><i class="fa-solid fa-calendar-plus"></i> Prendre un rendez-vous</h3>
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= $erreur ?></div>
                <?php endif; ?>

                <form method="post" id="form-rdv">
                    <div class="mb-3">
                        <label class="form-label">Médecin</label>
                        <select name="medecin_id" id="medecin_id" class="form-select" required>
                            <option value="">-- Choisir un médecin --</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= $medecinPreselectionne === (int)$m['id'] ? 'selected' : '' ?>>
                                    Dr <?= nettoyer($m['prenom'] . ' ' . $m['nom']) ?> — <?= nettoyer($m['specialite'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date souhaitée</label>
                        <input type="date" name="date_rdv" id="date_rdv" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Créneaux disponibles</label>
                        <div id="zone-creneaux" class="p-2 border rounded bg-white">
                            <p class="text-muted mb-0">Sélectionnez un médecin et une date pour voir les créneaux disponibles.</p>
                        </div>
                        <input type="hidden" name="heure_rdv" id="heure_rdv" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motif de la consultation (facultatif)</label>
                        <textarea name="motif" class="form-control" rows="3" placeholder="Ex : contrôle de routine, douleurs..."></textarea>
                    </div>

                    <button type="submit" id="btn-valider-rdv" class="btn btn-clinique w-100" disabled>
                        <i class="fa-solid fa-check"></i> Confirmer le rendez-vous
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($medecinPreselectionne): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // force le rechargement des créneaux si une date est déjà choisie
        const dateInput = document.getElementById('date_rdv');
        if (dateInput.value) dateInput.dispatchEvent(new Event('change'));
    });
</script>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
