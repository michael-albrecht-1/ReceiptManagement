<?php

require_once __DIR__ . '/../inc/configDB.php';

class DBService {
    // (A) CONSTRUCTOR - CONNECT TO DATABASE
    public $pdo = null;
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
}
