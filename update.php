<?php
include './functions.php';
include './config.php';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    updateAirDB($conn);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
