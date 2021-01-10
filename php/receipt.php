<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }
?>

<h1>Ajouter / modifier un ticket</h1>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
