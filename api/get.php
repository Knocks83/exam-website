<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once '../database.php';
include_once '../api.php';

if (!isset($_POST['table'])) {
    http_response_code(400);
    echo json_encode(
        array('error' => 'Table not found')
    );
    die();
}

// Create DB connection and Api object
$db_object = new Database();
$db = $db_object->getConnection();
$api = new Api($db, $_POST['table']);

// Fetch query and the number of rows
$stmt = $api->get();

try {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $finalArr = array();
    $finalArr['count'] = count($results);
    $finalArr['values'] = array();

    foreach ($results as $result) {
        $vals = [];
        foreach ($result as $index => $value) {
            $vals[$index] = $value;
        }

        array_push($finalArr['values'], $vals);
    }

    http_response_code(200);
    echo json_encode($finalArr);
} catch (PDOException $e) {
    http_response_code(404);
    echo json_encode(
        array('error' => htmlspecialchars($e->getMessage()))
    );
}
