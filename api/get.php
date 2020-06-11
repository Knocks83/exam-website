<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once './database.php';
include_once './api.php';

// Get the POST data and decode it
$data = json_decode(file_get_contents("php://input"), true);

// If it gets no raw data, check if they're sent via form
if (empty($data)) {
    if (isset($_POST['table'])) {
        $data['table'] = $_POST['table'];
    }
}

// If the table value is not found, return an error and stop the execution
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
