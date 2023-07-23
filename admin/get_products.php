<?php
include '../connection.php';

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Content-Type");

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Fetch products from db
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

if ($result) {
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // Generate the image URL based on the server location and the product image filename
        $row['productImage'] = 'http://localhost/pharmacy_app_api/images/' . basename($row['productImage']);
        $products[] = $row;
    }

    echo json_encode($products);
} else {
    $response = array("success" => false, "message" => "Failed to get products.");
    echo json_encode($response);
}

mysqli_close($conn);
