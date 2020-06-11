<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once './database.php';
include_once './api.php';

$data = json_decode(file_get_contents("php://input"), true);

// If the table value is not found, return an error and stop the execution
if (!isset($data['city'])) {
    http_response_code(400);
    echo json_encode(
        array('error' => 'City field not found')
    );
    die();
}

// Create DB connection and Api object
$db_object = new Database();
$db = $db_object->getConnection();
$api = new Api($db, null);

// Fetch query and the number of rows
$stmt = $api->getCoords($data['city']);

try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($results);
} catch (PDOException $e) {
    http_response_code(404);
    echo json_encode(
        array('error' => htmlspecialchars($e->getMessage()))
    );
}
