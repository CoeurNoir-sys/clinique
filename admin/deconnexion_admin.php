<?php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_nom']);
header('Location: connexion_admin.php');
exit;
