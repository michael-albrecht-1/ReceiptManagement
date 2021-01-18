<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <script id="_carbonads_projs" type="text/javascript" src="https://srv.carbonads.net/ads/CKYIE23N.json?segment=placement:bootswatchcom&amp;callback=_carbonads_go"></script>
    <title>Stop tickets</title>
</head>
<body>
    <div class="container">

<?php
  require __DIR__ . '/config.php';

  session_start();

  // 
  $requete = "SELECT * FROM `users`";
  $reponse = mysqli_query($conn,$requete) or die(mysql_error());
  if ($reponse->num_rows === 0) {
      require __DIR__ . "/firstLogin.php"; 
  } else 
  {
    if (isset($_POST['username'])){
      $username = stripslashes($_REQUEST['username']);
      $username = mysqli_real_escape_string($conn, $username);
      $password = stripslashes($_REQUEST['password']);
      $password = mysqli_real_escape_string($conn, $password);
        $query = "SELECT * FROM `users` WHERE username='$username' and password='".hash('sha256', $password)."'";
      $result = mysqli_query($conn,$query) or die(mysql_error());
      $rows = mysqli_num_rows($result);
      if($rows==1){
          $_SESSION['username'] = $username;
          header("Location: ../index.php");
      }else{
        $message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
      }
    }
  ?>
  <form class="box" action="" method="post" name="login">
      <h1 class="box-title">Connexion</h1>
      <div class="form-group">
          <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur">
      </div>

      <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Mot de passe">
      </div>
      
      <div class="form-group">
          <input type="submit" value="Connexion " name="submit" class="btn btn-primary">
      </div>
      <?php if (! empty($message)) { ?>
          <p class="text-danger"><?php echo $message; ?></p>
      <?php } ?>
  </form>

<?php
  }
  require __DIR__ . '/inc/footer.tpl.php';
?>