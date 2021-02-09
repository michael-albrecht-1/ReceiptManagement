<?php

    // ============================================================================
    // receipt update =============================================================
    // ============================================================================
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

    if (isset($_GET['photo'])){
        $preloadSrc = 'pictures/' . $_GET['photo'];
    }
    // ============================================================================
    // receipt update end==========================================================
    // ============================================================================

    // ============================================================================
    // saveReceipt ================================================================
    // ============================================================================
    if (isset($_POST['upload']) || isset($_POST['checkReceiptAndSelectNext'])) {
        $id = filter_input(INPUT_POST, 'receiptid');
        $date_emission = filter_input(INPUT_POST, 'date');
        $category = filter_input(INPUT_POST, 'receiptCategory');
        $provider = filter_input(INPUT_POST, 'provider');
        $amountTTC = filter_input(INPUT_POST, 'amountTTC');
        $tva = filter_input(INPUT_POST, 'tva');
        $description = filter_input(INPUT_POST, 'description');
        filter_input(INPUT_POST, 'ischecked') === "true" ? $isChecked = 1 : $isChecked = 0;
        $photo_name = $_FILES["photo"]["name"];
        $photo_data = $_FILES['photo']['tmp_name'];
        $isPhotoSaved = false;
        $isReceiptSaved = false;
        if (isset($_POST['checkReceiptAndSelectNext'])) {
            $isChecked = 1;
        }

        if ( ($photo_name != null) && ($photo_data != null) ){
            $isPhotoSaved = $receiptService->uploadImg($photo_name, $photo_data);
        } else if ($photo_name == '' && $photo_data == '' && $id != '') {
            $photo_name = $_POST['uploadSrc'];
            $isPhotoSaved = true;
          }else {
            $msg = sendMessage("La photo n'a pas pu être téléchargée.", 'danger');
        } 

        if ($isPhotoSaved != false ){
            if ($id == '') { // create receipt
                $isReceiptSaved = $receiptService->createReceipt($photo_name, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description);
            } else // update receipt
            {  
                $isReceiptSaved = $receiptService->updateReceipt($id, $photo_name, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description);
            }
        }
 
        if ($isPhotoSaved) {
            if ($isReceiptSaved){
                $msg = sendMessage("Le ticket est sauvegardé ! ");
            }
        }

        // if a receipt have been saved correctly and the button check and next was clicked we go to the next receipt to check
        if ($isReceiptSaved && isset($_POST['checkReceiptAndSelectNext'])) {
            $nextReceiptToCheck = $receiptService->getFirstReceiptToCheck();
            $nextReceiptToCheckLink = $receiptService->getLinkWithParamsFromRow($nextReceiptToCheck);
            if ($nextReceiptToCheck != null) {
            header("Location: $nextReceiptToCheckLink");
            $msg = sendMessage("Ticket pointé !");
            } else {
            $msg = sendMessage("Tous les tickets ont été pointés !");
            }
        }
    }
    // ============================================================================
    // saveReceipt end=============================================================
    // ============================================================================
?>

<?php 
  if( isset($_GET['id']) ) {
    echo "<h1>Modif du ticket n°" . $_GET['id'] . "</h1>";
  } else {
    echo "<h1>Ajout d'un ticket</h1>"; 
  }  

  echo $receiptService->msg ?? "";
  echo $msg ?? "";
?>

<form method="post" action="index.php" name="receipt" enctype="multipart/form-data">
    <input id="receiptid" name="receiptid" type="hidden" value="<?= $_GET['id'] ?? "" ?>">

    <fieldset>
    <div class="form-group row">
      <label for="photo">Prendre une photo</label>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo" capture="camera" <?= isset($_GET['photo']) ? "" : "required" ?>>
    </div>

    <div class="form-group row">
        <img id="preload" src='<?= $preloadSrc ?? '' ?>'/>
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
      
    <div class="row mb-4">
      <button type="submit" name="upload" class="btn btn-primary mr-2">Valider</button>
      <?php
      if (isset($_GET['id'])){
        $res = $receiptService->selectReceiptFromId($_GET['id']);
        
        if ($res['checked'] == 0) {
          echo '<button type="submit" name ="checkReceiptAndSelectNext" class="btn btn-info">Pointer et suivant</button>';
        }
      }
      ?>
    </div>   

</form>

<script src="js/tva.js"></script>
<script src="js/uploadReceipt.js"></script>

