<?php

class database
{
    private $conn = null;

    public function __construct()
    {
        include dirname(__FILE__) . '/../config.php';

        $conn = '';
        try {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->conn = $conn;
        } catch (PDOException $e) {
        }
    }

    // get the database connection
    public function getConnection()
    {
        return $this->conn;
    }
}
