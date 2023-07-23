<?php
require_once('../connection.php');

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

header("Content-Type:application/json");

$sql = "SELECT * FROM orders";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $orders = array();

        while ($row = mysqli_fetch_assoc($result)) {
            // Fetch delivery coordinates as an array
            $deliveryCoordinates = array(
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude']
            );

            // Fetch items for the order
            $orderItemsSql = "SELECT * FROM order_items WHERE orderId = " . $row['id'];
            $orderItemsResult = mysqli_query($conn, $orderItemsSql);

            $items = array();
            while ($itemRow = mysqli_fetch_assoc($orderItemsResult)) {
                $items[] = $itemRow;
            }

            $order = array(
                'id' => $row['id'],
                'userId' => $row['userId'],
                'name' => $row['name'],
                'phoneNumber' => $row['phoneNumber'],
                'address' => $row['address'],
                'createdAt' => $row['created_at'],
                'deliveryCoordinates' => $deliveryCoordinates,
                'items' => $items
            );

            $orders[] = $order;
        }

        http_response_code(200);
        echo json_encode($orders);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No orders found.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to fetch orders.']);
}

mysqli_close($conn);
?>
