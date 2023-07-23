<?php
include '../connection.php';

// Set headers to allow cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Check if the product ID is provided in the request
if (!isset($_GET['id'])) {
    // Product ID is missing
    $response = [
        'success' => false,
        'message' => 'Product ID is required!'
    ];
    echo json_encode($response);
    exit;
}

$id = $_GET['id'];

// Check if the product exists in the database
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the current product details
    $product = $result->fetch_assoc();

    // Retrieve the updated product details from the request body
    $productName = $_POST['productName'];
    $productType = $_POST['productType'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];

    // Handle the product image upload
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Check if the file upload was successful
        if ($file['error'] === UPLOAD_ERR_OK) {
            $tempFilePath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Generate a unique file name to avoid conflicts
            $newFileName = uniqid() . '.' . $fileExtension;

            // Specify the directory where the uploaded file will be moved
            $targetFilePath = 'C:/xampp/htdocs/pharmacy_app_api/images/' . $newFileName;

            // Move the temporary file to the target directory
            if (move_uploaded_file($tempFilePath, $targetFilePath)) {
                // Update the product image in the database
                $query = "UPDATE products SET productImage = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $newFileName, $id);
                $stmt->execute();
            }
        }
    }

    // Update the other product details in the database
    $query = "UPDATE products SET productName = ?, productType = ?, price = ?, description = ?, quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssdssi', $productName, $productType, $price, $description, $quantity, $id);
    $stmt->execute();

    // Return a success response
    $response = [
        'success' => true,
        'message' => 'Product updated successfully!'
    ];
    echo json_encode($response);
} else {
    // Product not found
    $response = [
        'success' => false,
        'message' => 'Product not found!'
    ];
    echo json_encode($response);
}
