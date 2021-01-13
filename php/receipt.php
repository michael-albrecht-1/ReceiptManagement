<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';
    require __DIR__ . '/inc/functions.php';
    require __DIR__ . '/inc/data.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }

    // en modif d'un ticket, on récupère le nom de la photo
    if ( isset($_GET['photo']) ) {
      $srcPhoto = "../pictures/".basename($_GET['photo']);
    }

    // en modif d'un ticket récupérer si c'est pointé ou pas
       if (isset($_GET['isChecked'])) {
        if ($_GET['isChecked'] == "oui") {
          $isCheckedYes = "checked";
          $isCheckedNo = "";
        } else {
          $isCheckedYes = "";
          $isCheckedNo = "checked";
        }
       } else {
        $isCheckedYes = "";
        $isCheckedNo = "checked";
       }


    // traitement de l'envoi du formulaire
    if (isset($_POST['upload'])) {
        if (isset($_FILES['photo']['name']) && ($_FILES['photo']['name'] != '')) {
          // On récupère le nom de l'image
          $picture = $_FILES['photo']['name'];
          // répertoire de stockage des images
          $target = "../pictures/".basename($picture);
        } else  {
          $picture = $_POST['uploadSrc'];
        }
        // Pour les mises à jour
        isset($_POST['id']) ?  $id = $_POST['id'] : $id = "";

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
        
        if ($_POST['receiptid'] == "") { 
          $query = "INSERT INTO `receipts`(`photo`, `date_emission`, `type`, `montant_ttc`, `tva`, `checked`, `description`) VALUES ('$picture','$date','$type',$amountTTC,'$tva','$isChecked','$description')";
          $result = mysqli_query($conn,$query);
        }else { // A TESTER
          $id = $_POST['receiptid'];
          $query = "UPDATE `receipts` SET `photo`='$picture', `date_emission`='$date', `type`='$type', `montant_ttc`=$amountTTC, `tva`='$tva', `checked`='$isChecked', `description`='$description' WHERE `receipts`.id = '$id'";
          $result = mysqli_query($conn,$query);
        }

        
        // On importe l'image
        if (isset($_FILES['photo']['name']) && ($_FILES['photo']['name'] != '')) {
          if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            sendMessage("L'import de l'image n'a pas fonctionné !", "danger");
          }
        }

        if ($result) {
          $msg = sendMessage("Ticket enregistré ! ", "success");
        }else{
          $msg = sendMessage("Ça n'a pas fonctionné ! ", "danger");
        }
    }
?>

<?php 
  if( isset($_GET['id']) ) {
    echo "<h1>Modif du ticket n°" . $_GET['id'] . "</h1>";
  } else {
    echo "<h1>Ajout d'un ticket</h1>"; 
  }  

?>

<?= $msg ?? "" ?>

<form method="post" action="receipt.php" name="receipt" enctype="multipart/form-data">
    <input id="receiptid" name="receiptid" type="hidden" value="<?= $_GET['id'] ?? "" ?>">
    <fieldset>
    <div class="form-group row">
      <label for="photo">Prendre une photo</label>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo" capture="camera" <?= isset($_GET['photo']) ? "" : "required" ?>>
    </div>

    <div class="form-group row">
        <img id="preload" src="<?= $srcPhoto ?? "" ?>">
        <input id="uploadSrc" name="uploadSrc" type="hidden" value="<?= $_GET['photo'] ?? "" ?>">
    </div>
    
  

    <div class="form-group row">
      <label for="date">Date</label>
      <input type="date" class="form-control" id="date" name="date"  value="<?= $_GET['date'] ?? date("Y-m-d") ?>" required>
    </div>
    
    <div class="form-group row">
      <label for="type">Type</label>
      <select class="form-control" id="type" name="type"> 
        <?php foreach ($receiptTypes as $index => $type){ 
            $selected = "";
            
            if (isset($_GET['type'])) {
              $_GET['type'] == $type ? $selected = "selected" : $selected = ""; 
            } else {
              if (isset($_POST['type'])) {
                $_POST['type'] == $index ? $selected = "selected" : $selected = ""; 
              } 
            }
            echo "<option value=" . $index . " " . $selected . ">" . $type . "</option>";
        }
        ?>
      </select>
    </div>

    <div class="form-group row">
      <label for="montant">Montant TTC</label>
      <input type="text" class="form-control" id="amountTTC" name="amountTTC" placeholder="Saisir le montant TTC" value="<?= $_GET['amount'] ?? "" ?>" required>
    </div>    

    <fieldset class="form-group">
      <legend>TVA</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input tva-check" name="tva" id="tva1" value="tva1">
          0
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input tva-check" name="tva" id="tva2" value="tva2">
          5.5
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input tva-check" name="tva" id="tva3" value="tva3">
          10
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input tva-check" name="tva" id="tva4" value="tva4">
          20
        </label>
      </div>
    </fieldset>

    <fieldset class="form-group">
      <legend>Pointé</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="ischecked" id="oui" value="true" <?= $isCheckedYes ?>>
          oui
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="ischecked" id="non" value="false" <?= $isCheckedNo ?>>
          non
        </label>
      </div>
    </fieldset>

    <div class="form-group">
      <label for="exampleTextarea">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"><?= $_GET['description'] ?? "" ?></textarea>
    </div>

    <button type="submit" name="upload" class="btn btn-primary">Valider</button>

</form>

<script src="../js/uploadReceipt.js"></script>

<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
