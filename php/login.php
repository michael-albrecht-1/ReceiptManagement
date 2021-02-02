<?php


// 
$isFirstConnecion = $authService->isFirstConnection();
  
  
if ($isFirstConnecion) {
    require __DIR__ . "/firstLogin.php"; 
} else 
{
    if (isset($_POST['username'])){
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        if ($username != '' && $password != '') {
            $isLogginsCorrects = $authService->checkLogins($username, $password);
            if($isLogginsCorrects){
                $_SESSION['username'] = $username;
                header("Location: index.php");
            } 
        } else {
        $message = "Le nom d'utilisateur et le mot de passe doivent être renseignés";
        } 
    }
  ?>
   <h1 class="box-title mt-4">Connexion</h1>

<div class="row">
    <?= $authService->msg ?? ""; ?>
</div>
  <form class="box" action="" method="post" name="login">
      <div class="form-group">
          <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur">
      </div>

      <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Mot de passe">
      </div>
      
      <div class="form-group">
          <input type="submit" value="Connexion " name="submit" class="btn btn-primary">
      </div>
      <?php if (! empty($message)): ?>
          <p class="text-danger"><?php echo $message; ?></p>
      <?php endif; ?>
  </form>

<?php } ?>