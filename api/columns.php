<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once './database.php';
include_once './api.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['table'])) {
    http_response_code(400);
    echo json_encode(
        array('error' => 'Table field not found')
    );
    die();
}

// Create DB connection and Api object
$db_object = new Database();
$db = $db_object->getConnection();
$api = new Api($db, $data['table']);

// Fetch query and the number of rows
$cols = $api->cols;

try {
    $finalArr = array();
    $finalArr['count'] = count($cols);
    $finalArr['columns'] = $cols;

    http_response_code(200);
    echo json_encode($finalArr);
} catch (PDOException $e) {
    http_response_code(404);
    echo json_encode(
        array('error' => htmlspecialchars($e->getMessage()))
    );
}
