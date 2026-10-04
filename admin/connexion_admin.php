<?php
require '../config.php';
require '../includes/fonctions.php';
$baseUrl = '../';
$titrePage = 'Connexion administrateur';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomUtilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE nom_utilisateur = ?");
    $stmt->execute([$nomUtilisateur]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($motDePasse, $admin['mot_de_passe'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nom'] = $admin['nom_utilisateur'];
        header('Location: dashboard.php');
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion administrateur - <?= NOM_CLINIQUE ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center" style="min-height:100vh; background:var(--bleu-fonce);">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-form p-4">
                <h4 class="text-center mb-4" style="color:var(--bleu-fonce)"><i class="fa-solid fa-user-shield"></i> Espace administrateur</h4>
                <?php if ($erreur): ?>
                    <div class="alert alert-danger"><?= $erreur ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Identifiant</label>
                        <input type="text" name="nom_utilisateur" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-clinique w-100">Connexion</button>
                </form>
                <p class="text-center small mt-3 mb-0"><a href="../index.php">&larr; Retour au site</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
