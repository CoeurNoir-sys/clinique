<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titrePage ?> - Administration - <?= NOM_CLINIQUE ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-clinique">
    <div class="container-fluid px-4">
        <span class="navbar-brand"><i class="fa-solid fa-staff-snake"></i> <?= NOM_CLINIQUE ?> — Administration</span>
        <span class="text-white">
            Connecté : <?= nettoyer($_SESSION['admin_nom']) ?> &nbsp;
            <a href="deconnexion_admin.php" class="btn btn-sm btn-accent">Déconnexion</a>
        </span>
    </div>
</nav>

<div class="d-flex">
    <div class="sidebar-admin" style="width:230px;">
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-gauge"></i> Tableau de bord</a>
        <a href="rendez-vous.php" class="<?= basename($_SERVER['PHP_SELF']) === 'rendez-vous.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-days"></i> Rendez-vous</a>
        <a href="medecins.php" class="<?= basename($_SERVER['PHP_SELF']) === 'medecins.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-doctor"></i> Médecins</a>
    </div>
    <div class="flex-grow-1 p-4">
