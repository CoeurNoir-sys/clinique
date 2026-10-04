<?php
require '../config.php';
require '../includes/fonctions.php';
redirigerSiNonAdmin();
$titrePage = 'Médecins';

$erreur = '';
$succes = '';

// Suppression
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM medecins WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: medecins.php?supprime=1');
    exit;
}

// Ajout / modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $specialiteId = (int)($_POST['specialite_id'] ?? 0) ?: null;
    $email = nettoyer($_POST['email'] ?? '');
    $telephone = nettoyer($_POST['telephone'] ?? '');

    if (!$nom || !$prenom) {
        $erreur = "Le nom et le prénom sont obligatoires.";
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE medecins SET nom=?, prenom=?, specialite_id=?, email=?, telephone=? WHERE id=?");
            $stmt->execute([$nom, $prenom, $specialiteId, $email, $telephone, $id]);

            // Mise à jour des disponibilités (Lun-Ven, horaires fournis)
            $heureDebut = $_POST['heure_debut'] ?? '08:00';
            $heureFin = $_POST['heure_fin'] ?? '16:00';
            $pdo->prepare("DELETE FROM disponibilites WHERE medecin_id = ?")->execute([$id]);
            $stmtDispo = $pdo->prepare("INSERT INTO disponibilites (medecin_id, jour_semaine, heure_debut, heure_fin) VALUES (?, ?, ?, ?)");
            foreach ([1,2,3,4,5] as $jour) {
                $stmtDispo->execute([$id, $jour, $heureDebut, $heureFin]);
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO medecins (nom, prenom, specialite_id, email, telephone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $specialiteId, $email, $telephone]);
            $nouvelId = $pdo->lastInsertId();

            $heureDebut = $_POST['heure_debut'] ?? '08:00';
            $heureFin = $_POST['heure_fin'] ?? '16:00';
            $stmtDispo = $pdo->prepare("INSERT INTO disponibilites (medecin_id, jour_semaine, heure_debut, heure_fin) VALUES (?, ?, ?, ?)");
            foreach ([1,2,3,4,5] as $jour) {
                $stmtDispo->execute([$nouvelId, $jour, $heureDebut, $heureFin]);
            }
        }
        header('Location: medecins.php?succes=1');
        exit;
    }
}

$specialites = $pdo->query("SELECT * FROM specialites ORDER BY nom")->fetchAll();
$medecins = $pdo->query("
    SELECT m.*, s.nom AS specialite
    FROM medecins m
    LEFT JOIN specialites s ON m.specialite_id = s.id
    ORDER BY m.nom
")->fetchAll();

require '../includes/header_admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 style="color:var(--bleu-fonce)">Gestion des médecins</h3>
    <button class="btn btn-clinique" data-bs-toggle="modal" data-bs-target="#modalMedecin" onclick="reinitialiserFormulaire()">
        <i class="fa-solid fa-plus"></i> Ajouter un médecin
    </button>
</div>

<?php if (isset($_GET['succes'])): ?><div class="alert alert-success">Enregistré avec succès.</div><?php endif; ?>
<?php if (isset($_GET['supprime'])): ?><div class="alert alert-info">Médecin supprimé.</div><?php endif; ?>
<?php if ($erreur): ?><div class="alert alert-danger"><?= $erreur ?></div><?php endif; ?>

<div class="table-responsive">
    <table class="table table-clinique bg-white align-middle">
        <thead>
            <tr><th>Nom</th><th>Spécialité</th><th>Email</th><th>Téléphone</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($medecins as $m): ?>
                <tr>
                    <td>Dr <?= nettoyer($m['prenom'] . ' ' . $m['nom']) ?></td>
                    <td><?= nettoyer($m['specialite'] ?? '-') ?></td>
                    <td><?= nettoyer($m['email'] ?? '-') ?></td>
                    <td><?= nettoyer($m['telephone'] ?? '-') ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"
                            onclick='remplirFormulaire(<?= json_encode($m) ?>)'
                            data-bs-toggle="modal" data-bs-target="#modalMedecin">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <a href="?supprimer=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce médecin ? Ses rendez-vous seront également supprimés.')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal ajout/modification -->
<div class="modal fade" id="modalMedecin" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="titreModal">Ajouter un médecin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="f_id">
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" id="f_nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" id="f_prenom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Spécialité</label>
                        <select name="specialite_id" id="f_specialite" class="form-select">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($specialites as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= nettoyer($s['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="f_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="f_telephone" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Début de journée</label>
                            <input type="time" name="heure_debut" id="f_heure_debut" class="form-control" value="08:00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Fin de journée</label>
                            <input type="time" name="heure_fin" id="f_heure_fin" class="form-control" value="16:00">
                        </div>
                    </div>
                    <p class="small text-muted mb-0">Disponibilités appliquées du lundi au vendredi.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-clinique">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function reinitialiserFormulaire() {
    document.getElementById('titreModal').textContent = 'Ajouter un médecin';
    document.getElementById('f_id').value = '';
    document.getElementById('f_nom').value = '';
    document.getElementById('f_prenom').value = '';
    document.getElementById('f_specialite').value = '';
    document.getElementById('f_email').value = '';
    document.getElementById('f_telephone').value = '';
    document.getElementById('f_heure_debut').value = '08:00';
    document.getElementById('f_heure_fin').value = '16:00';
}
function remplirFormulaire(m) {
    document.getElementById('titreModal').textContent = 'Modifier le médecin';
    document.getElementById('f_id').value = m.id;
    document.getElementById('f_nom').value = m.nom;
    document.getElementById('f_prenom').value = m.prenom;
    document.getElementById('f_specialite').value = m.specialite_id || '';
    document.getElementById('f_email').value = m.email || '';
    document.getElementById('f_telephone').value = m.telephone || '';
}
</script>

<?php require '../includes/footer_admin.php'; ?>
