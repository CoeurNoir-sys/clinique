<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titrePage) ? $titrePage . ' - ' . NOM_CLINIQUE : NOM_CLINIQUE ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?? '' ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-clinique">
    <div class="container">
        <a class="navbar-brand" href="<?= $baseUrl ?? '' ?>index.php">
            <i class="fa-solid fa-staff-snake"></i> <?= NOM_CLINIQUE ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?? '' ?>index.php">Accueil</a></li>
                <?php if (estConnecte()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?? '' ?>prendre-rdv.php">Prendre RDV</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?? '' ?>mes-rendez-vous.php">Mes rendez-vous</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?? '' ?>deconnexion.php">Déconnexion (<?= nettoyer($_SESSION['patient_prenom']) ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?? '' ?>connexion.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?? '' ?>inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
