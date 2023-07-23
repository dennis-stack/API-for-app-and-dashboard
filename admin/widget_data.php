<?php
require_once('../connection.php');

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$type = $_GET['type'];

if ($type === "user") {
    $sql = "SELECT COUNT(*) AS count FROM users";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $amount = $row['count'];
        $data = ['amount' => $amount];
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Failed to fetch widget data.']);
    }
} else if ($type === "order") {
    $sql = "SELECT COUNT(*) AS count FROM orders";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $amount = $row['count'];
        $data = ['amount' => $amount];
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Failed to fetch widget data.']);
    }
} else if ($type === "product") {
    $sql = "SELECT COUNT(*) AS count FROM products";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $amount = $row['count'];
        $data = ['amount' => $amount];
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Failed to fetch widget data.']);
    }
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Invalid widget type.']);
}

mysqli_close($conn);
?>
