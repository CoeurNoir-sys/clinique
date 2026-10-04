<?php
require 'config.php';
require 'includes/fonctions.php';
$titrePage = 'Inscription';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom'] ?? '');
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = nettoyer($_POST['telephone'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    if (!$nom || !$prenom || !$email || !$telephone || !$motDePasse) {
        $erreur = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse e-mail invalide.";
    } elseif (strlen($motDePasse) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($motDePasse !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse e-mail est déjà utilisée.";
        } else {
            $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO patients (nom, prenom, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $telephone, $hash]);

            $_SESSION['patient_id'] = $pdo->lastInsertId();
            $_SESSION['patient_prenom'] = $prenom;
            header('Location: index.php');
            exit;
        }
    }
}

require 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-form p-4">
                <h3 class="text-center mb-4" style="color:var(--bleu-fonce)">Créer un compte patient</h3>
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= $erreur ?></div>
                <?php endif; ?>
                <form method="post" id="form-inscription">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required value="<?= nettoyer($_POST['nom'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required value="<?= nettoyer($_POST['prenom'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse e-mail</label>
                        <input type="email" name="email" class="form-control" required value="<?= nettoyer($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" required value="<?= nettoyer($_POST['telephone'] ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmation</label>
                            <input type="password" id="confirmation" name="confirmation" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-clinique w-100">S'inscrire</button>
                </form>
                <p class="text-center mt-3 mb-0">Déjà inscrit(e) ? <a href="connexion.php">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
