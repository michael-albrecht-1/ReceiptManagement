<?php

if (isset($_POST['adminusername'], $_POST['adminemail'], $_POST['adminpassword'])){
    // récupérer le nom d'utilisateur et supprimer les antislashes ajoutés par le formulaire
    $username = trim($_POST['adminusername']);
    $email = trim($_POST['adminemail']);
    $password = trim($_POST['adminpassword']);
    $authService->registerUser($username, $email, $password);
} else 
{

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