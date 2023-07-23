<?php
// Include the database connection file
require_once('../connection.php');

// Get the order data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Extract the order details
$name = $data['name'];
$email = $data['email'];
$phoneNumber = $data['phoneNumber'];
$latitude = $data['latitude'];
$longitude = $data['longitude'];
$address = $data['address'];
$items = $data['items'];

// Check if all required fields are present
if (empty($name) || empty($email) || empty($phoneNumber) || empty($latitude) || empty($longitude) || empty($address) || empty($items)) {
    // Return an error response
    http_response_code(400);
    echo json_encode(['message' => 'Error submitting the order!']);
    exit();
}

// Split the name into first name and last name
$nameParts = explode(' ', $name);
$firstName = $nameParts[0];
$lastName = $nameParts[1];

// Retrieve the user's ID from the users table
$userSql = "SELECT id FROM users WHERE firstName = '$firstName' AND lastName = '$lastName'";
$userResult = mysqli_query($conn, $userSql);

// Check if the query was successful and the user exists
if ($userResult && mysqli_num_rows($userResult) > 0) {
    $user = mysqli_fetch_assoc($userResult);
    $userId = $user['id'];

    // Prepare the SQL query to insert the order details into the database
    $orderSql = "INSERT INTO orders (userId, name, email, phoneNumber, latitude, longitude, address) VALUES ('$userId', '$name', '$email', '$phoneNumber', '$latitude', '$longitude', '$address')";
    $orderResult = mysqli_query($conn, $orderSql);

    // Check if the query was successful
    if ($orderResult) {
        // Get the auto-generated order ID
        $orderId = mysqli_insert_id($conn);

        // Insert the ordered items into the order_items table
        foreach ($items as $item) {
            $itemName = $item['itemName'];
            $quantity = $item['quantity'];
            $totalPrice = $item['totalPrice'];

            // Prepare the SQL query to insert the ordered item
            $itemSql = "INSERT INTO order_items (orderId, itemName, quantity, totalPrice) VALUES ('$orderId', '$itemName', '$quantity', '$totalPrice')";
            mysqli_query($conn, $itemSql);
        }

        // Return a success response
        http_response_code(200);
        echo json_encode(['message' => 'Order submitted successfully.']);
    } else {
        // Return an error response
        http_response_code(500);
        echo json_encode(['message' => 'Error submitting the order.']);
    }
} else {
    // User not found
    http_response_code(404);
    echo json_encode(['message' => 'User not found.']);
}

// Close the database connection
mysqli_close($conn);
?>
