<?php
require_once('../connection.php');

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'total_sales') {
    $sql = "SELECT SUM(oi.totalPrice) AS amount FROM orders o INNER JOIN order_items oi ON o.id = oi.orderId";
} elseif ($type === 'prev_month_sales') {
    $sql = "SELECT SUM(oi.totalPrice) AS amount FROM orders o INNER JOIN order_items oi ON o.id = oi.orderId WHERE MONTH(o.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid request']);
    exit;
}

$result = mysqli_query($conn, $sql);

if ($result) {
    $data = mysqli_fetch_assoc($result);
    $amount = (float) $data['amount'];

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['amount' => $amount]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to fetch data']);
}

mysqli_close($conn);
?>
