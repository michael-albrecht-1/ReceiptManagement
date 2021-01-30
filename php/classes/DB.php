<?php

require_once __DIR__ . '/../inc/configDB.php';

class DB {
  // (A) CONSTRUCTOR - CONNECT TO DATABASE
  private $pdo = null;
  private $stmt = null;
  public $error = "";
  function __construct () {
    try {
      $this->pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, 
        DB_USER, DB_PASSWORD, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  // (B) DESTRUCTOR - CLOSE DATABASE CONNECTION
  function __destruct () {
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  function queryFetchAll ($sql) {
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  function queryFetch ($sql) {
    return $this->pdo->query($sql)->fetch();
  }

  function getFirstReceiptToCheck() {
    $sql = "SELECT * 
    FROM receipts
    WHERE checked=false
    ORDER BY `date_emission` ASC, `id` ASC
    LIMIT 1;";
      
    return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
  }

  function saveReceipt() {
    if (isset($_FILES['photo']['name']) && ($_FILES['photo']['name'] != '')) {
        $picture = $_FILES['photo']['name'];
        $target = "pictures_/".basename($picture);
    } else  {
        $picture = $_POST['uploadSrc'];
    }

    // WIP
    $picture_data = 'WIP';

    $_POST['ischecked'] === "true" ? $isChecked = 1 : $isChecked = 0;
    if (isset($_POST['checkReceiptAndSelectNext'])) {
        $isChecked = 1;
    }

    if ($_POST['receiptid'] == "") { 
        $sth = $this->pdo->prepare('INSERT INTO `receipts`(`photo_name`, `photo_data`, `date_emission`, `category`, `provider`, `montant_ttc`, `tva`, `checked`, `description`) VALUES (:photo_name, :photo_data, :date, :receiptCategory, :provider, :amountTTC, :tva, :isChecked, :description)');

    }else { 
        $sth = $this->pdo->prepare('UPDATE `receipts` SET `photo_name`=:photo_name, `photo_data`=:photo_data, `date_emission`=:date, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');
        $sth->bindParam(':id', $_POST['receiptid'], PDO::PARAM_STR);          
    }
    
    $sth->bindParam(':photo_name', $picture, PDO::PARAM_STR);
    $sth->bindParam(':photo_data', $picture_data, PDO::PARAM_STR);
    $sth->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
    $sth->bindParam(':receiptCategory', $_POST['receiptCategory'], PDO::PARAM_STR);
    $sth->bindParam(':provider', $_POST['provider'], PDO::PARAM_STR);
    $sth->bindParam(':amountTTC', $_POST['amountTTC'], PDO::PARAM_INT);
    $sth->bindParam(':tva', $_POST['tva'], PDO::PARAM_STR);
    $sth->bindParam(':isChecked', $isChecked, PDO::PARAM_STR);
    $sth->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
    $result = $sth->execute();

    // On importe l'image ===
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
        $nextReceiptToCheck = $this->getFirstReceiptToCheck();
        $receiptCategories = ["restaurant", "gasoil", "hôtel", "péage", "autre"];  
        $nextReceiptToCheckLink = getLinkWithParamsFromRow($nextReceiptToCheck, $receiptCategories);
        if ($nextReceiptToCheck != null) {
        header("Location: $nextReceiptToCheckLink");
        $msg = sendMessage("Ticket pointé ! ", "success");
        } else {
        $msg = sendMessage("Tous les tickets ont été pointés ! ", "success");
        }
    }
  }

  // (C) SAVE IMAGE (FROM UPLOAD)
  function saveImg () {
    try {
      $this->stmt = $this->pdo->prepare(
        "INSERT INTO `images` (`img_name`, `img_data`) VALUES (?,?)"
      );
      $this->stmt->execute([
        $_FILES["upload"]["name"], file_get_contents($_FILES['upload']['tmp_name'])
      ]);
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }
    return true;
  }

  // (D) GET IMAGE
  function getImg ($name) {
    $this->stmt = $this->pdo->prepare(
      "SELECT `img_data` FROM `images` WHERE `img_name`=?"
    );
    $this->stmt->execute([$name]);
    $img = $this->stmt->fetch();
    return $img['img_data'];
  }
  
  // (E) GENERATE BASE64 ENCODED HTML TAG
  function showImg ($name) {
    $img = base64_encode($this->get($name));
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    echo "<img src='data:image/jpg;base64,".$img."'/>";
  }
}
