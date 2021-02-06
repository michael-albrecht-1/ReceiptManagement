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

    function createReceipt($photo_name, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description) {
        try {
            $sth = $this->db->pdo->prepare('INSERT INTO `receipts`(`photo_name`, `date_emission`, `category`, `provider`, `montant_ttc`, `tva`, `checked`, `description`) VALUES (:photo_name, :date_emission, :receiptCategory, :provider, :amountTTC, :tva, :isChecked, :description)');
                    
            $sth->bindParam(':photo_name',  $photo_name, PDO::PARAM_STR);
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
        return true;       
    }

    function updateReceipt($id, $photo_name, $date_emission, $category, $provider, $amountTTC, $tva, $isChecked, $description) {
        try {
            $sth = $this->db->pdo->prepare('UPDATE `receipts` SET `photo_name`=:photo_name, `date_emission`=:date_emission, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');

            $sth->bindParam(':id', $id, PDO::PARAM_STR);  
            $sth->bindParam(':photo_name',  $photo_name, PDO::PARAM_STR);
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
        return true;
    }
  
    //  get photo name
    function uploadImg( $photo_name, $photo_data)
    {
        $uploadedFile = $photo_data; 
        $sourceProperties = getimagesize($uploadedFile);
        $dirPath = "pictures/";
        $ext = pathinfo($photo_name, PATHINFO_EXTENSION);
        $imageType = $sourceProperties[2];


        switch ($imageType) {


            case IMAGETYPE_PNG:
                $imageSrc = imagecreatefrompng($uploadedFile); 
                $tmp = $this->imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
                return imagepng($tmp,$dirPath. $photo_name);

            case IMAGETYPE_JPEG:
                $imageSrc = imagecreatefromjpeg($uploadedFile); 
                $tmp = $this->imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
                return imagejpeg($tmp,$dirPath. $photo_name);
            
            case IMAGETYPE_GIF:
                $imageSrc = imagecreatefromgif($uploadedFile); 
                $tmp = $this->imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
                return imagegif($tmp,$dirPath. $photo_name);

            default:
                $this->msg =  "Invalid Image type.";
                exit;
                break;
        }
    }

    function imageResize($imageSrc,$imageWidth,$imageHeight) {

        $newImageWidth =800;
        $ratio = $imageWidth / $newImageWidth;
        $newImageHeight = $imageHeight / $ratio;
    
        $newImageLayer=imagecreatetruecolor($newImageWidth,$newImageHeight);
        imagecopyresampled($newImageLayer,$imageSrc,0,0,0,0,$newImageWidth,$newImageHeight,$imageWidth,$imageHeight);
    
        return $newImageLayer;
    }

    function getLinkWithParamsFromRow($row) {

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