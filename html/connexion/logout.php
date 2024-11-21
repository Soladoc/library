<?php
require_once 'auth.php';

se_deconnecter();


// Rediriger vers la page de connexion ou la page d'accueil
header('Location: /');
exit;
