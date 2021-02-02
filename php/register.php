<?php


    // Vérifiez que l'utilisateur est connecté avec le compte "admin", sinon redirigez-le vers la page de connexion
    if ($_SESSION['username'] !== 'admin') {
        header("Location: index.php");
    }
    

    if (isset($_POST['username'], $_POST['email'], $_POST['password'])){
    // récupérer le nom d'utilisateur et supprimer les antislashes ajoutés par le formulaire
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $res = $authService->registerUser($username, $email, $password);
    }else{



?>

    <form method="post">
        <h1 class="box-title">Créer un compte</h1>
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur" required />
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="email" placeholder="Email" required />
        </div>  
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Mot de passe" required />
        </div>
        <button type="submit" name="submit" class="btn btn-primary">S'inscrire</button>
    </form>

<?php } 

    require __DIR__ . '/inc/footer.tpl.php';
?>
