<?php

class ReceiptService {
    private $db = null;
    public $msg = "";
    private $receiptListFilters;
    
    function __construct () {
        $this->db = new DBService();
        $this->receiptListFilters = 0;
    }

    function selectReceiptFromId($id) {
        $query = 'SELECT * FROM `receipts` WHERE (`id` = :currentId)';
        $values = [':currentId' => $id];

        try
        {
            $res = $this->db->pdo->prepare($query);
            $res->execute($values);
        }
        catch (PDOException $e)
        {
            $this->msg = sendMessage("Query error : selectReceiptFromId function");
            die();
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    function getFirstReceiptToCheck() {
        $sql = "SELECT * 
        FROM receipts
        WHERE checked=false
        ORDER BY `date_emission` ASC, `id` ASC
        LIMIT 1;";
        
        return $this->db->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    function createReceipt($photo_name, $photo_data, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description) {
        try {
            $sth = $this->db->pdo->prepare('INSERT INTO `receipts`(`photo_name`, `photo_data`, `date_emission`, `category`, `provider`, `amountTTC`, `tva`, `checked`, `description`) VALUES (:photo_name, :photo_data, :date_emission, :receiptCategory, :provider, :amountTTC, :tva, :isChecked, :description)');
                    
            $sth->bindParam(':photo_name',  $photo_name, PDO::PARAM_STR);
            $sth->bindParam(':photo_data', $photoData);
            $sth->bindParam(':date_emission', $date_emission, PDO::PARAM_STR);
            $sth->bindParam(':receiptCategory', $category, PDO::PARAM_STR);
            $sth->bindParam(':provider', $provider, PDO::PARAM_STR);
            $sth->bindParam(':amountTTC', $amountTTC, PDO::PARAM_INT);
            $sth->bindParam(':tva', $tva, PDO::PARAM_STR);
            $sth->bindParam(':isChecked', $isChecked, PDO::PARAM_STR);
            $sth->bindParam(':description', $description, PDO::PARAM_STR);
            $sth->execute(); 
        } catch (Exception $ex) {
            $this->msg = sendMessage($ex->getMessage(), 'danger');
            return false;
        }
        $this->msg= sendMessage("Ticket enregistré ! ", "success");
        return true;       
    }

    function updateReceipt($id, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description, $photo_name = null, $photo_data = null) {
        try {
            if ( $photo_name != null && $photo_data != null ){
                $sth = $this->db->pdo->prepare('UPDATE `receipts` SET `photo_name`=:photo_name, `photo_data`=:photo_data, `date_emission`=:date_emission, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');
                $sth->bindParam(':photo_name',  $photo_name, PDO::PARAM_STR);
                $sth->bindParam(':photo_data', $photoData);
            } else {
                $sth = $this->db->pdo->prepare('UPDATE `receipts` SET `date_emission`=:date_emission, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');
            }
            $sth->bindParam(':id', $id, PDO::PARAM_STR);  
            $sth->bindParam(':date_emission', $date_emission, PDO::PARAM_STR);
            $sth->bindParam(':receiptCategory', $category, PDO::PARAM_STR);
            $sth->bindParam(':provider', $provider, PDO::PARAM_STR);
            $sth->bindParam(':amountTTC', $amountTTC, PDO::PARAM_INT);
            $sth->bindParam(':tva', $tva, PDO::PARAM_STR);
            $sth->bindParam(':isChecked', $isChecked, PDO::PARAM_STR);
            $sth->bindParam(':description', $description, PDO::PARAM_STR);
            $sth->execute(); 

        } catch (Exception $ex) {
            $this->msg = sendMessage($ex->getMessage(), 'danger');
            return false;
        }
        $this->msg= sendMessage("Ticket enregistré ! ", "success");
        return true;
    }
  
    //  get photo name
    function getImg ($name) {
        $this->stmt = $this->db->pdo->prepare(
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

    function getLinkWithParamsFromRow($row, $receiptCategories) {

        $category = formatCategory($row['category'], $receiptCategories);
        $tva = formatTva($row['tva']);
        $row['checked'] ? $isChecked = "oui" : $isChecked = "non";
        $provider = urlencode($row['provider'] );
        $description = urlencode($row['description']);
      
        return 'index.php' .
                    '?id=' . $row['id'] .
                    '&photo=' . $row['photo_name'] .
                    '&date=' . $row['date_emission'] .
                    '&receiptCategory=' . $category .
                    '&provider=' . $provider .
                    '&tva=' . $tva .
                    '&amount=' . $row['montant_ttc'] .
                    '&isChecked=' . $isChecked .
                    '&description=' . $description;
    }

    function getFilteredReceiptList($total_records_per_page, $offset) {
        if (isset($_GET['isChecked']) ) 
        {
            $isChecked = $_GET['isChecked'];
            if ( $isChecked == "isChecked-yes") {
                $checkedSQL = "checked=true";
            } elseif ( $isChecked == "isChecked-no") {
                $checkedSQL = "checked=false";
            } 
            else {
                $checkedSQL = "checked=true OR checked=false";
            }
            setcookie("isChecked", $checkedSQL,  time() + 2592000);
            setcookie("isCheckedJS", $_GET['isChecked'],  time() + 2592000);
            $this->receiptListFilters = 'WHERE ' . $checkedSQL;
            $sql = "SELECT * FROM `receipts` $this->receiptListFilters ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
        } elseif ( isset($_COOKIE['isChecked']) ){
            $this->receiptListFilters = 'WHERE ' . $_COOKIE["isChecked"];
            $sql = "SELECT * FROM `receipts` $this->receiptListFilters ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
        } else {
            $sql = "SELECT * FROM `receipts` ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
        }

        return $this->db->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function getTotalReceiptsNumber() {
        if ( isset($_GET['isChecked']) || isset($_COOKIE['isChecked']) ){
            $sql = "SELECT COUNT(*) As total_records FROM `receipts` $this->receiptListFilters";
        } else {
            $sql = "SELECT COUNT(*) As total_records FROM `receipts`";
        }
    
        return $this->db->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

}