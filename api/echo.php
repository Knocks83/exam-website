<?php
// Set required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the used libraries
include_once './database.php';
include_once './api.php';

// Echo a json_encode of the get array
if (!empty($_GET)) {
    // Set response code and give response
    http_response_code(200);
    echo json_encode($_GET);
} else {
    // If no records found, give a 404 and an error message
    http_response_code(404);
    echo json_encode(
        array('error' => 'No get parameters found.')
    );
}
