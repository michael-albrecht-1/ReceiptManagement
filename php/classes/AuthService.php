<?php

class AuthService {
    private $db = null;
    public $msg = "";

    function __construct () {
        $this->db = new DBService();
    }



    function isFirstConnection () {
        $sql = "SELECT * FROM `users`";
        $res = $this->db->pdo->query($sql);
        return $res->rowCount() > 0 ? false : true;
    }

    function registerUser($username, $email, $password) {
        $password=password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT into `users` (`username`, `email`, `password`) VALUES (:username, :email, :password)";
        $sth = $this->db->pdo->prepare($sql);
        
        $sth->bindParam(':username', $username, PDO::PARAM_STR);
        $sth->bindParam(':email', $email, PDO::PARAM_STR);
        $sth->bindValue(':password', $password, PDO::PARAM_STR);
        $res =  $sth->execute();

        if($res){
            if ($username === 'admin'){
                echo "<div class='sucess'>
                <h3>Le compte a été créé avec succès.</h3>
                <p>Cliquez ici pour vous <a href='index.php'>connecter</a></p>
                </div>";
            } else {
                echo "<div class='sucess'>
                <h3>Le compte a été créé avec succès.</h3>
                <p>Cliquez <a href='index.php'>ici</a> pour vous retourner à l'accueil</p>
                </div>";
            }
            
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
        $res = $this->db->pdo->prepare($query);
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
}
