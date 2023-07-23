<?php
require_once('connection.php');

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Delete the order from the orders table
    $deleteOrderSql = "DELETE FROM orders WHERE id = '$orderId'";
    $deleteOrderResult = mysqli_query($conn, $deleteOrderSql);

    // Delete the associated items from the order_items table
    $deleteItemsSql = "DELETE FROM order_items WHERE orderId = '$orderId'";
    $deleteItemsResult = mysqli_query($conn, $deleteItemsSql);

    if ($deleteOrderResult && $deleteItemsResult) {
        http_response_code(200);
        echo json_encode(['message' => 'Order deleted successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to delete order.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Missing order ID in the request.']);
}

mysqli_close($conn);
?>
