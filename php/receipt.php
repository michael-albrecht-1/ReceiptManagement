<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }

    // traitement de l'envoi du formulaire
    if (isset($_POST['upload'])) {
        // On récupère le nom de l'image
        $picture = $_FILES['photo']['name'];
        // répertoire de stockage des images
    	$target = "../pictures/".basename($picture);

        // Date
        $date = $_POST['date'];

        // Type
        $type =  $_POST['type'];

        // Montant TTC
        $amountTTC = $_POST['amountTTC'];

        // Taux de TVA
        $tva = $_POST['tva'];

        // Pointé ou non en compta
        $_POST['ischecked'] === "true" ? $isChecked = 1 : $isChecked = 0;

        // Description
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $query = "INSERT INTO `receipts`(`photo`, `date_emission`, `type`, `montant_ttc`, `tva`, `checked`, `description`) VALUES ('$picture','$date','$type',$amountTTC,'$tva','$isChecked','$description')";
        $result = mysqli_query($conn,$query);

        
        // On importe l'image
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target) && $result) {
            $msg = "<p class=\"text-success text-center\">Ticket enregistré</p>";
        }else{
            $msg = "<p class=\"text-danger text-center\">Ça n'a pas fonctionné !</p>";
        }
    }
?>

<h1>Ajouter / modifier un ticket</h1>

<?= $msg ?? "" ?>

<form method="post" action="receipt.php" name="receipt" enctype="multipart/form-data">
    <fieldset>
    <div class="form-group row">
      <label for="photo">Prendre une photo</label>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo" capture="camera" onchange="loadFile(event) form="receipt" required>
    </div>

    <div class="form-group row">
        <img id="preload">
    </div>
    
    <div class="form-group row">
      <label for="date">Date</label>
      <input type="date" class="form-control" id="date" name="date"  value="<?= date("Y-m-d") ?>" required>
    </div>
    
    <div class="form-group row">
      <label for="type">Type</label>
      <select class="form-control" id="type" name="type">
        <option value="1">Restaurant</option>
        <option value="2">Gasoil</option>
        <option value="3">Hôtel</option>
        <option value="4">Péage</option>
        <option value="5">Autre</option>
      </select>
    </div>

    <div class="form-group row">
      <label for="montant">Montant TTC</label>
      <input type="text" class="form-control" id="amountTTC" name="amountTTC" placeholder="Saisir le montant TTC" required>
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
          <input type="radio" class="form-check-input" name="ischecked" id="oui" value="true">
          oui
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="ischecked" id="non" value="false" checked="">
          non
        </label>
      </div>
    </fieldset>

    <div class="form-group">
      <label for="exampleTextarea">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"></textarea>
    </div>

    <button type="submit" name="upload" class="btn btn-primary">Valider</button>

</form>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
