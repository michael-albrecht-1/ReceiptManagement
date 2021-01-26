<?php

    // en modif d'un ticket, on récupère le nom de la photo
    if ( isset($_GET['photo']) ) {
      $srcPhoto = "pictures/".basename($_GET['photo']);
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
    if (isset($_POST['upload']) || isset($_POST['checkReceiptAndSelectNext'])) {
        if (isset($_FILES['photo']['name']) && ($_FILES['photo']['name'] != '')) {
          // On récupère le nom de l'image
          $picture = $_FILES['photo']['name'];
          // répertoire de stockage des images
          $target = "pictures/".basename($picture);
        } else  {
          $picture = $_POST['uploadSrc'];
        }
        $date = $_POST['date'];
        $receiptCategory =  $_POST['receiptCategory'];
        $provider = mysqli_real_escape_string($conn, $_POST['provider']);
        $amountTTC = $_POST['amountTTC'];
        $tva = $_POST['tva'];
        $_POST['ischecked'] === "true" ? $isChecked = 1 : $isChecked = 0;
        if (isset($_POST['checkReceiptAndSelectNext'])) {
          $isChecked = 1;
        }
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        if ($_POST['receiptid'] == "") { 
          $query = "INSERT INTO `receipts`(`photo`, `date_emission`, `category`, `provider`, `montant_ttc`, `tva`, `checked`, `description`) VALUES ('$picture','$date','$receiptCategory', '$provider', $amountTTC,'$tva','$isChecked','$description')";
          $result = mysqli_query($conn,$query);
        }else { 
          $id = $_POST['receiptid'];
          $query = "UPDATE `receipts` SET `photo`='$picture', `date_emission`='$date', `category`='$receiptCategory', `provider`='$provider', `montant_ttc`=$amountTTC, `tva`='$tva', `checked`='$isChecked', `description`='$description' WHERE `receipts`.id = '$id'";
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

      // if button check and next was clicked we go to the next receipt to check
      if (isset($_POST['checkReceiptAndSelectNext'])) {
        $nextReceiptToCheck = getFirstReceiptToCheck($conn);
        $nextReceiptToCheckLink = getLinkWithParamsFromRow($nextReceiptToCheck, $receiptCategories);
        if ($nextReceiptToCheck != null) {
          header("Location: $nextReceiptToCheckLink");
          $msg = sendMessage("Ticket pointé ! ", "success");
        } else {
          $msg = sendMessage("Tous les tickets ont été pointés ! ", "success");
        }
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

<form method="post" action="index.php" name="receipt" enctype="multipart/form-data">
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
      <select class="form-control" id="receiptCategory" name="receiptCategory"> 
        <?php foreach ($receiptCategories as $index => $category){ 
            $selected = "";
            
            if (isset($_GET['receiptCategory'])) {
              $_GET['receiptCategory'] == $category ? $selected = "selected" : $selected = ""; 
            } else {
              if (isset($_POST['receiptCategory'])) {
                $_POST['receiptCategory'] == $index ? $selected = "selected" : $selected = ""; 
              } 
            }
            echo "<option value=" . $index . " " . $selected . ">" . $category . "</option>";
        }
        ?>
      </select>
    </div>

    <div class="form-group row">
      <label for="montant">Fournisseur</label>
      <input type="text" class="form-control" id="provider" name="provider" placeholder="Saisir le nom du fournisseur" value="<?= $_GET['provider'] ?? "" ?>" required>
    </div>    
    
    <div class="form-group row">
      <label for="montant">Montant TTC</label>
      <input type="number" step="0.01" class="form-control" id="amountTTC" name="amountTTC" placeholder="Saisir le montant TTC" value="<?= $_GET['amount'] ?? "" ?>" required>
    </div>    

    <div class="row">
      <fieldset class="form-group col-6">
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
            <input type="radio" class="form-check-input tva-check" name="tva" id="tva3" value="tva3" checked>
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

      <fieldset class="form-group col-6">
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
    </div>
    

    <div class="form-group">
      <label for="exampleTextarea">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"><?= $_GET['description'] ?? "" ?></textarea>
    </div>

    <button type="submit" name="upload" class="btn btn-primary mb-4">Valider</button>
    <?php
    if (isset($_GET['id'])){
      $currentId = $_GET['id'];
      $req = "SELECT * FROM receipts WHERE id=$currentId";
      $result = mysqli_query($conn, $req);
      $row = mysqli_fetch_array($result);
      
      if ($row['checked'] == 0) {
        echo '<button type="submit" name ="checkReceiptAndSelectNext" class="btn btn-info">Pointer et suivant</button>';
      }
    }
      
      ?>
    

</form>

<script src="js/tva.js"></script>
<script src="js/uploadReceipt.js"></script>

