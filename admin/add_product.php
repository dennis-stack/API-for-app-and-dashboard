<?php
include '../connection.php';

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Function to sanitize and validate input data
function sanitizeInput($input)
{
  $input = trim($input);
  $input = stripslashes($input);
  $input = htmlspecialchars($input);
  return $input;
}

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    // Return early for preflight requests
    http_response_code(200);
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Retrieve and sanitize form data
  $productName = sanitizeInput($_POST["productName"]);
  $productType = sanitizeInput($_POST["productType"]);
  $price = sanitizeInput($_POST["price"]);
  $description = sanitizeInput($_POST["description"]);
  $quantity = sanitizeInput($_POST["quantity"]);

  // Process the image file
  $file = $_FILES["file"];
  $fileName = $file["name"];
  $fileTmpName = $file["tmp_name"];
  $fileError = $file["error"];

  // Check if an image file is uploaded successfully
  if ($fileError === 0) {
    // Move the uploaded image to a desired location
    $destination = "C:/xampp/htdocs/pharmacy_app_api/images/" . $fileName;
    move_uploaded_file($fileTmpName, $destination);

    // Modify the destination to be saved in the database
    $databaseDestination = "http://dynamic-ip.ddns.net/pharmacy_app_api/images/" . basename($destination);

    // Check if a product with the same name and image already exists
    $existingProductQuery = "SELECT * FROM products WHERE productName = ? AND productImage = ?";
    $existingProductStmt = $conn->prepare($existingProductQuery);
    $existingProductStmt->bind_param("ss", $productName, $databaseDestination);
    $existingProductStmt->execute();
    $existingProductResult = $existingProductStmt->get_result();

    if ($existingProductResult->num_rows > 0) {
      // Product with the same name and image already exists
      echo "A product with the same name and image already exists.";
    } else {
      // Perform the database query to insert the product data
      $insertQuery = "INSERT INTO products (productImage, productName, productType, price, description, quantity) VALUES (?, ?, ?, ?, ?, ?)";
      $insertStmt = $conn->prepare($insertQuery);
      $insertStmt->bind_param("sssssi", $databaseDestination, $productName, $productType, $price, $description, $quantity);

      if ($insertStmt->execute()) {
        // Insertion successful
        echo "New product added successfully.";
      } else {
        // Insertion failed
        echo "Error adding new product: " . $insertStmt->error;
      }

      $insertStmt->close();
    }

    $existingProductStmt->close();
  } else {
    echo "Error uploading the image: " . $fileError;
  }
}
?>
