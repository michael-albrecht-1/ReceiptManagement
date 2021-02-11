<?php

class ReceiptService {
    private $db = null;
    private $totalReceiptsNumber;
    public $msg = '';
    private $receiptListFilters = '';
    
    public function __construct ()
    {
        $this->db = new DBService();
    }

    public function selectReceiptFromId($id)
    {
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

    function getFirstReceiptToCheck()
    {
        $sql = "SELECT * 
        FROM receipts
        WHERE checked=false
        ORDER BY `date_emission` ASC, `id` ASC
        LIMIT 1;";
        
        return $this->db->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    function createReceipt($photo_name, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description)
    {
        try {
            $sth = $this->db->pdo->prepare('INSERT INTO `receipts`(`photo_name`, `date_emission`, `category`, `provider`, `montant_ttc`, `tva`, `checked`, `description`) VALUES (:photo_name, :date_emission, :receiptCategory, :provider, :amountTTC, :tva, :isChecked, :description)');
                    
            $sth->bindParam(':photo_name',  $photo_name, PDO::PARAM_STR);
            $sth->bindParam(':date_emission', $date_emission, PDO::PARAM_STR);
            $sth->bindParam(':receiptCategory', $category, PDO::PARAM_STR);
            $sth->bindParam(':provider', $provider, PDO::PARAM_STR);
            $sth->bindParam(':amountTTC', $amountTTC, PDO::PARAM_STR);
            $sth->bindParam(':tva', $tva, PDO::PARAM_STR);
            $sth->bindParam(':isChecked', $isChecked, PDO::PARAM_STR);
            $sth->bindParam(':description', $description, PDO::PARAM_STR);
            $sth->execute(); 
        } catch (Exception $ex) {
            $this->msg = sendMessage($ex->getMessage(), 'danger');
            return false;
        }
        return true;       
    }

    function updateReceipt($id, $photo_name, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description)
    {
        try {
            $sth = $this->db->pdo->prepare('UPDATE `receipts` SET `photo_name`=:photo_name, `date_emission`=:date_emission, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');

            $sth->bindParam(':id', $id, PDO::PARAM_STR);  
            $sth->bindParam(':photo_name',  $photo_name, PDO::PARAM_STR);
            $sth->bindParam(':date_emission', $date_emission, PDO::PARAM_STR);
            $sth->bindParam(':receiptCategory', $category, PDO::PARAM_STR);
            $sth->bindParam(':provider', $provider, PDO::PARAM_STR);
            $sth->bindParam(':amountTTC', $amountTTC, PDO::PARAM_STR);
            $sth->bindParam(':tva', $tva, PDO::PARAM_STR);
            $sth->bindParam(':isChecked', $isChecked, PDO::PARAM_STR);
            $sth->bindParam(':description', $description, PDO::PARAM_STR);
            $sth->execute(); 

        } catch (Exception $ex) {
            $this->msg = sendMessage($ex->getMessage(), 'danger');
            return false;
        }
        return true;
    }
  
    //  get photo name
    function uploadImg( $photo_name, $photo_data)
    {
        $imgInfo = getimagesize($photo_data);
        $mime = $imgInfo['mime']; 
        if ($mime == 'image/jpeg' || $mime == 'image/png'){
            if (move_uploaded_file($photo_data, 'pictures/' . $photo_name)) {
                return true;
            } else {
                return false;
                $this->msg = sendMessage('La photo ne s\'est pas téléchargée ! Opération abandonée. ', 'danger');
            }
        } else {
            $this->msg = sendMessage('Le format du fichier n\'est pas supporté ! Opération abandonée. ', 'danger');
        }

    }

    function getLinkWithParamsFromRow($row)
    {

        $category = formatCategory($row['category']);
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

    public function getFilteredReceiptList($filters, $total_records_per_page, $offset)
    {
        if ( is_array($filters) ) 
        {
            extract($filters);
            $_SESSION['start-date'] = $startDate;
            $_SESSION['end-date'] = $endDate;
            $_SESSION['is-checked'] = $isChecked;
        } 
        
        if ( isset($_SESSION['is-checked']) ){
            $formatedStartDate = date("Ymd", strtotime($_SESSION['start-date']));
            $formatedEndDate = date("Ymd", strtotime($_SESSION['end-date']));
            $this->getReceiptFilters($formatedStartDate, $formatedEndDate, $_SESSION['is-checked']);
        }
        $this->setTotalReceiptNumber();
        $sql = "SELECT * FROM `receipts` $this->receiptListFilters ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
        return $this->db->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getReceiptFilters($formatedStartDate, $formatedEndDate, $isChecked)
    {
        if ( $_SESSION['is-checked'] == 'true') {
            $checkedSQL = '`checked`=true';
        } elseif ( $_SESSION['is-checked'] == 'false') {
            $checkedSQL = "`checked`=false";
        } 
        else {
            $checkedSQL = "`checked`=true OR `checked`=false";
        }

        $this->receiptListFilters = 'WHERE (' . $checkedSQL . ') AND `date_emission` >= ' . $formatedStartDate . ' AND `date_emission` <= ' . $formatedEndDate ;
    }

    private function setTotalReceiptNumber()
    {
        $sql = "SELECT COUNT(*) As total_records FROM `receipts` $this->receiptListFilters";
        $this->totalReceiptsNumber = $this->db->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalReceiptsNumber()
    {
        return $this->totalReceiptsNumber;
    }

    public function getDateFilter($date)
    {
        if ( isset($_GET[$date]) ){
            return  $_GET[$date];
        } else if ( isset($_SESSION[$date]) ){
            return  $_SESSION[$date];
        } else {
            if ( $date == 'start-date') {
                return date("Y-m-d", mktime(0, 0, 0, 1, 1, 2000));
            } else if ($date == 'end-date'){
                return date("Y-m-d");
            }
        }
    }

    public function getIsCHeckedFilter($value)
    {
        if ( isset($_GET['is-checked']) ){
            if (  $_GET['is-checked'] == $value){
                return  'checked';
            }
        } else if ( isset($_SESSION['is-checked']) ){
            if (  $_SESSION['is-checked'] == $value){
                return  'checked';
            }
        } else {
            if ( $value == 'both') {
                return  'checked';
            } 
            return '';
        }
    }
}
