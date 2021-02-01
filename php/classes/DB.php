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

    function isFirstConnection () {
        $sql = "SELECT * FROM `users`";
        $res = $this->pdo->query($sql);
        return $res->rowCount() > 0 ? false : true;
    }

    function registerUser($username, $email, $password) {
        $password=password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT into `users` (`username`, `email`, `password`) VALUES (:username, :email, :password)";
        $sth = $this->pdo->prepare($sql);
        
        $sth->bindParam(':username', $username, PDO::PARAM_STR);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->bindValue(':password', $password, PDO::PARAM_STR);
        $res =  $sth->execute();

        if($res){
            echo "<div class='sucess'>
                    <h3>Le compte a été créé avec succès.</h3>
                    <p>Cliquez ici pour vous <a href='index.php'>connecter</a></p>
            </div>";
        } else {
            $this->msg = sendMessage("Ca n'a pas fonctionné ! ");
        }
    }

    function checkLogins($username, $password) {

        /* Look for the username in the database. */
        $query = 'SELECT * FROM `users` WHERE (`username` = :name)';

        /* Values array for PDO. */
        $values = [':name' => $username];

        /* Execute the query */
        try
        {
        $res = $this->pdo->prepare($query);
        $res->execute($values);
        }
        catch (PDOException $e)
        {
        /* Query error. */
        $this->msg = sendMessage("Query error.");
        die();
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        /* If there is a result, check if the password matches using password_verify(). */
        if (is_array($row))
        {
            if (password_verify($password, $row['password']))
            {
                return true;
            } else {
                $this->msg = sendMessage("Le mot de passe est erronné ! ");
                return false;
            }
        } else {
            $this->msg = sendMessage("Cet utilisateur n'existe pas ! ");
            return false;
        } 
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

            if ($_POST['receiptid'] == "") { // new receipt
                $sth = $this->pdo->prepare('INSERT INTO `receipts`(`photo_name`, `photo_data`, `date_emission`, `category`, `provider`, `montant_ttc`, `tva`, `checked`, `description`) VALUES (:photo_name, :photo_data, :date, :receiptCategory, :provider, :amountTTC, :tva, :isChecked, :description)');
                
                $photoData = file_get_contents($_FILES['photo']['tmp_name']);
                $sth->bindParam(':photo_name',  $_FILES["photo"]["name"], PDO::PARAM_STR);
                $sth->bindParam(':photo_data', $photoData);

            }else {  // update receipt
                if ( ($_FILES['photo']['tmp_name'] != '') && ($_FILES['photo']['tmp_name'] != '') ){ // if new photo 
                    $sth = $this->pdo->prepare('UPDATE `receipts` SET `photo_name`=:photo_name, `photo_data`=:photo_data, `date_emission`=:date, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');
                    $photoData = file_get_contents($_FILES['photo']['tmp_name']);
                    $sth->bindParam(':photo_name',  $_FILES["photo"]["name"], PDO::PARAM_STR);
                    $sth->bindParam(':photo_data', $photoData);   
                } else { // no new photo
                    $sth = $this->pdo->prepare('UPDATE `receipts` SET `date_emission`=:date, `category`=:receiptCategory, `provider`=:provider, `montant_ttc`=:amountTTC, `tva`=:tva, `checked`=:isChecked, `description`=:description WHERE `receipts`.`id` = :id');
                }
                $sth->bindParam(':id', $_POST['receiptid'], PDO::PARAM_STR);  
            }


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
