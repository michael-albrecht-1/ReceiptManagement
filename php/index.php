<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Initialiser la session
    session_start();
    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }
?>

<div class="sucess">
    <h1>Liste des tickets</h1>
    <?php echo $_SESSION['username']??""; ?>
    <a href="logout.php">Déconnexion</a>
</div>

<button class="btn btn-primary">azaz</button>




<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
