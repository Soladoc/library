<?php 
session_start() ?>
<header>
    <div class="logo">
        <!-- Todo: redireiger vers la bonne page selon si l'user est connecté ou non -->
        <a href="accPro.php"><img src="../images/logo.png" alt="Logo pact"></a>
    </div>
    <?php if (isset($_SESSION['log']) && $_SESSION['log'] === true){ ?>
    <a href="../connexion/logout.php">
        <div class="auth-button">
            <img src="../images/profile-icon.png" alt="Profil">
            <span>Déconnexion</span>
        </div>
    </a>
    <?php }else{ ?>
    <a href="connexion.php">
        <div class="auth-button">
            <img src="../images/profile-icon.png" alt="Profil">
            <span>Connexion</span>
        </div>
    </a>
    <?php }?>
</header>
