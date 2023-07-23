<?php
include '../connection.php';

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Content-Type");

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Check if the product ID is provided in the request
if (!isset($_GET['id'])) {
    $response = array("success" => false, "message" => "Product ID is required!");
    echo json_encode($response);
    exit;
}

$id = $_GET['id'];

// Fetch the product details from the database
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $product = $result->fetch_assoc();
    // Generate the image URL based on the server location and the product image filename
    $product['productImage'] = 'http://localhost/pharmacy_app_api/images/' . basename($product['productImage']);
    echo json_encode($product);
} else {
    $response = array("success" => false, "message" => "Product not found!");
    echo json_encode($response);
}

$stmt->close();
$conn->close();
?>
