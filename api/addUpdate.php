<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once './database.php';
include_once './api.php';

// Get the POST data and decode it
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['table']) || !isset($data['values'])) {
    http_response_code(400);
    echo json_encode(
        array('error' => 'Missing parameters')
    );
    die();
}

// Create DB connection and Api object
$db_object = new Database();
$db = $db_object->getConnection();
$api = new Api($db, $data['table']);

// Fetch query and the number of rows
$result = $api->addUpdate($data['values']);

if ($result) {
    http_response_code(200);
    echo json_encode(
        array(
            'result' => 'success'
        )
    );
} else {
    http_response_code(404);
    echo json_encode(
        array('error' => 'Value not found')
    );
}
