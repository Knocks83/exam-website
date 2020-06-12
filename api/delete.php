<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once './database.php';
include_once './api.php';

// Get the POST data and decode it
$data = json_decode(file_get_contents("php://input"), true);

// If it gets no raw data, check if they're sent via get
if (empty($data)) {
    if (isset($_GET['table']) && isset($_GET['column']) && isset($_GET['value'])) {
        $data['table'] = $_GET['table'];
        $data['column'] = $_GET['column'];
        $data['value'] = $_GET['value'];
    }
}

// If the required values are not found, return an error and stop the execution
if (!isset($data['table']) || !isset($data['column']) || !isset($data['value'])) {
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
$result = $api->delete($data['column'], $data['value']);

if ($result >= 0) {
    http_response_code(200);
    echo json_encode(
        array(
            'count' => $result,
            'result' => 'success'
        )
    );
} else {
    http_response_code(404);
    echo json_encode(
        array('error' => 'Error while deleting data!')
    );
}
