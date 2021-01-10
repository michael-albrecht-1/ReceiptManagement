<?php

if (isset($_REQUEST['adminusername'], $_REQUEST['adminemail'], $_REQUEST['adminpassword'])){
    // récupérer le nom d'utilisateur et supprimer les antislashes ajoutés par le formulaire
   
    
    $username = stripslashes("admin");
    $username = mysqli_real_escape_string($conn, $username); 
    // récupérer l'email et supprimer les antislashes ajoutés par le formulaire
    $email = stripslashes($_REQUEST['adminemail']);
    $email = mysqli_real_escape_string($conn, $email);
    // récupérer le mot de passe et supprimer les antislashes ajoutés par le formulaire
    $password = stripslashes($_REQUEST['adminpassword']);
    $password = mysqli_real_escape_string($conn, $password);
    //requéte SQL + mot de passe crypté
    $query = "INSERT into `users` (username, email, password)
            VALUES ('$username', '$email', '".hash('sha256', $password)."')";
    // Exécuter la requête sur la base de données
    $res = mysqli_query($conn, $query);
    if($res){
    echo "<div>
            <h3>Compté créé avec succès.</h3>
            Pour rappel le nom d'utilisateur est \"admin\"
            <p>Cliquez ici pour vous <a href='login.php'>connecter</a></p>
    </div>";
    }
} else {

?>
    
<form method="post">
    <h1 class="box-title">Première utilisation - créer le compte administrateur</h1>
    <div class="form-group">
        <input type="text" class="form-control" name="adminusername" value="admin" readonly />
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="adminemail" placeholder="Email" required />
    </div>  
    <div class="form-group">
        <input type="password" class="form-control" name="adminpassword" placeholder="Mot de passe" required />
    </div>
    <button type="submit" name="submit" class="btn btn-primary">S'inscrire</button>
</form>

<?php } ?>