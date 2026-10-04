<?php
require 'config.php';
require 'includes/fonctions.php';
$titrePage = 'Connexion';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = ?");
    $stmt->execute([$email]);
    $patient = $stmt->fetch();

    if ($patient && password_verify($motDePasse, $patient['mot_de_passe'])) {
        $_SESSION['patient_id'] = $patient['id'];
        $_SESSION['patient_prenom'] = $patient['prenom'];
        header('Location: index.php');
        exit;
    } else {
        $erreur = "Adresse e-mail ou mot de passe incorrect.";
    }
}

require 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-form p-4">
                <h3 class="text-center mb-4" style="color:var(--bleu-fonce)">Connexion</h3>
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= $erreur ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Adresse e-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-clinique w-100">Se connecter</button>
                </form>
                <p class="text-center mt-3 mb-0">Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
                <p class="text-center small mt-2"><a href="admin/connexion_admin.php">Accès administrateur</a></p>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
