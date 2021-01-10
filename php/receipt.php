<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }

    // traitement de l'envoi du formulaire
    if (isset($_POST['photo']) && isset($_POST['montantTTC'])) {
        var_dump($_POST);
        var_dump($_FILES);
        // $filename = $_FILES['photo'][‘name’];

        // Date
        $date = $_POST['date'];

        // Montant TTC
        $montantTTC = $_POST['montantTTC'];

        // Taux de TVA
        $tva = $_POST['tva'];

        // Pointé ou non en compta
        $isChecked = boolval($_POST['checked']);
        var_dump($isChecked);

        // Description
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $query = "INSERT INTO `receipts`(`photo`, `date_emission`, `montant_ttc`, `tva`, `checked`, `description`) VALUES ('chemin photo2','$date',$montantTTC,'$tva','$isChecked','$description')";
        $result = mysqli_query($conn,$query) or die(mysql_error());
        $rows = mysqli_num_rows($result);
        var_dump($rows);
        /*if($rows==1){
            echo "la création de ticket a marché";
        }else{
            echo "la création de ticket a PAS marché !";
        }*/

    }
?>

<h1>Ajouter / modifier un ticket</h1>

<form method="post" name="receipt">
    <fieldset>
    <div class="form-group row">
      <label for="photo">Prendre une photo</label>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo"  onchange="loadFile(event)">
    </div>

    <div class="form-group row">
        <img id="preload">
    </div>
    
    <div class="form-group row">
      <label for="date">Date</label>
      <input type="date" class="form-control" id="date" name="date"  value="<?= date("Y-m-d") ?>" required>
    </div>
    
    <div class="form-group row">
      <label for="montant">Montant TTC</label>
      <input type="text" class="form-control" id="montantTTC" name="montantTTC" placeholder="Saisir le montant TTC" required>
    </div>    

    <fieldset class="form-group">
      <legend>TVA</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva1" value="tva1">
          5.5
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva2" value="tva2" checked="">
          10
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva3" value="tva3">
          20
        </label>
      </div>
    </fieldset>

    <fieldset class="form-group">
      <legend>Pointé</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="checked" id="oui" value="oui">
          oui
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="checked" id="non" value="non" checked="">
          non
        </label>
      </div>
    </fieldset>

    <div class="form-group">
      <label for="exampleTextarea">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Valider</button>

</form>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
