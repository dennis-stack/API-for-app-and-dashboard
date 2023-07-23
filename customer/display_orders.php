<?php
require_once('../connection.php');

$email = $_GET['email'];

$sql = "SELECT * FROM orders WHERE email = '$email' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $orders = array();

        // Fetch each order and add it to the array
        while ($row = mysqli_fetch_assoc($result)) {
            // Get the order ID
            $orderId = $row['id'];

            // Prepare the SQL query to fetch items for the order
            $itemSql = "SELECT * FROM order_items WHERE orderId = '$orderId'";
            $itemResult = mysqli_query($conn, $itemSql);

            // Create an array to store the items for the order
            $items = array();

            // Fetch each item and add it to the array
            while ($itemRow = mysqli_fetch_assoc($itemResult)) {
                $items[] = $itemRow;
            }

            // Add the order with its items to the orders array
            $formattedOrder = [
                'created_at' => $row['created_at'],
                'address' => $row['address'],
                'items' => $items
            ];

            $orders[] = $formattedOrder;
        }

        // Return the orders as JSON response
        $response = ['orders' => $orders];
        http_response_code(200);
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No orders found for the user.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Error fetching the orders.']);
}

mysqli_close($conn);
?>
