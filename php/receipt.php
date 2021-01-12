<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';
    require __DIR__ . '/inc/data.php';

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
            $msg = '<div class="col-lg-4 alert alert-success alert-dismissible text-center"><button type="button" class="close" data-bs-dismiss="alert">&times;</button>Ticket enregistré</div>';
        }else{
            $msg = '<div class="col-lg-4 alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-bs-dismiss="alert">&times;</button>Ça n\'a pas fonctionné !</div>';
        }
    }
?>

<h1>Ajouter / modifier un ticket</h1>

<?= $msg ?? "" ?>

<form method="post" action="receipt.php" name="receipt" enctype="multipart/form-data">
    <fieldset>
    <div class="form-group row">
      <label for="photo">Prendre une photo</label>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo" capture="camera" required>
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
        <?php foreach ($receiptTypes as $index => $type){ 
            $selected = "";
            if (isset($_POST['type'])) {
                $_POST['type'] == $index ? $selected = "selected" : $selected = ""; 
            } 
            echo "<option value=" . $index . " " . $selected . ">" . $type . "</option>";
        }
        ?>
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
          0
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva2" value="tva2">
          5.5
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva3" value="tva3" checked="">
          10
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva4" value="tva4">
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

<script src="../js/uploadReceipt.js"></script>

<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
