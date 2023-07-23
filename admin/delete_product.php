<?php
include '../connection.php';

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Content-Type");

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Get the user ID to delete from the request
$id = isset($_GET['id']) ? $_GET['id'] : '';

$query = "DELETE FROM products WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result) {
    $response = array("success" => true, "message" => "Product removed successfully.");
    echo json_encode($response);
} else {
    $response = array("success" => false, "message" => "Failed to remove product.");
    echo json_encode($response);
}

mysqli_close($conn);

?>