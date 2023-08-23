<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate a delay for demonstration purposes (remove in production)
sleep(1);

// Simulate a response (replace with your actual logic)
$response = array(
    'status' => 'success',
    'message' => 'Task section updated successfully.'
);

// Output the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>

