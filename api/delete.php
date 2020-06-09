<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once '../database.php';
include_once '../api.php';

if (!isset($_POST['table']) || !isset($_POST['column']) || !isset($_POST['value'])) {
    http_response_code(400);
    echo json_encode(
        array('error' => 'Missing parameters')
    );
    die();
}

// Create DB connection and Api object
$db_object = new Database();
$db = $db_object->getConnection();
$api = new Api($db, $_POST['table']);

// Fetch query and the number of rows
$result = $api->delete($_POST['column'], $_POST['value']);

if ($result > 0) {
    http_response_code(200);
    echo json_encode(
        array(
            'count' => $result,
            'result' => 'success')
    );
} else {
    http_response_code(404);
    echo json_encode(
        array('error' => 'Value not found')
    );
}
