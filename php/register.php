<?php

    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez que l'utilisateur est connecté avec le compte "admin", sinon redirigez-le vers la page de connexion
    if (true) {
        if ($_SESSION['username'] !== 'admin') {
            header("Location: logout.php");
        }
    }

    if (isset($_REQUEST['username'], $_REQUEST['email'], $_REQUEST['password'])){
    // récupérer le nom d'utilisateur et supprimer les antislashes ajoutés par le formulaire
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($conn, $username); 
    // récupérer l'email et supprimer les antislashes ajoutés par le formulaire
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($conn, $email);
    // récupérer le mot de passe et supprimer les antislashes ajoutés par le formulaire
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($conn, $password);
    //requéte SQL + mot de passe crypté
        $query = "INSERT into `users` (username, email, password)
                VALUES ('$username', '$email', '".hash('sha256', $password)."')";
    // Exécuter la requête sur la base de données
        $res = mysqli_query($conn, $query);
        if($res){
        echo "<div class='sucess'>
                <h3>Le compte a été créé avec succès.</h3>
                <p>Cliquez ici pour vous <a href='login.php'>connecter</a></p>
        </div>";
        }
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
