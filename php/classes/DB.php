<?php

require_once __DIR__ . '/../inc/configDB.php';

class DB {
  // (A) CONSTRUCTOR - CONNECT TO DATABASE
  private $pdo = null;
  private $stmt = null;
  public $msg = "";
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
    try {
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

        // WIP when update receipt but photo not changed : photo actually deleted
        $photoData = file_get_contents($_FILES['photo']['tmp_name']);
        $sth->bindParam(':photo_name',  $_FILES["photo"]["name"], PDO::PARAM_STR);
        $sth->bindParam(':photo_data', $photoData);

        $sth->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
        $sth->bindParam(':receiptCategory', $_POST['receiptCategory'], PDO::PARAM_STR);
        $sth->bindParam(':provider', $_POST['provider'], PDO::PARAM_STR);
        $sth->bindParam(':amountTTC', $_POST['amountTTC'], PDO::PARAM_INT);
        $sth->bindParam(':tva', $_POST['tva'], PDO::PARAM_STR);
        $sth->bindParam(':isChecked', $isChecked, PDO::PARAM_STR);
        $sth->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
        $result = $sth->execute();

    } catch (Exception $ex) {
        $this->msg = sendMessage($ex->getMessage(), 'danger');
        return false;
    }
    $this->msg= sendMessage("Ticket enregistré ! ", "success");
    return true;
  }

 
  //  get photo name
  function getImg ($name) {
    $this->stmt = $this->pdo->prepare(
      "SELECT `photo_data` FROM `receipts` WHERE `photo_name`=?"
    );
    $this->stmt->execute([$name]);
    $img = $this->stmt->fetch();
    return $img['photo_data'];
  }
  
  //  generate src for update receipt photo preload
  function getImgSrc ($name) {
    $img = base64_encode($this->getImg($name));
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    return 'data:image/jpg;base64,' . $img;
  }
}
