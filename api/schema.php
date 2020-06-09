<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once '../database.php';
include_once '../api.php';

// Create DB connection and Api object
$db_object = new Database();
$db = $db_object->getConnection();
$api = new Api($db, NULL);

// Fetch query and the number of rows
$schema = $api->schema();

$num = count($schema);

// If more than 0 record found, get the results
if ($num > 0) {

    $tables = [];
    $tables['count'] = $num;
    $tables['tables'] = [];

    foreach ($schema as $table) {
        $tables['tables'][] = $table[0];
    }

    // Set response code and give response
    http_response_code(200);
    echo json_encode($tables);
} else {
    // If no records found, give a 404 and an error message
    http_response_code(404);
    echo json_encode(
        array('error' => 'No tables found.')
    );
}
