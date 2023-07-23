<?php
require_once('../connection.php');

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Generate a list of all months
$months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Initialize the chart data array
$chartData = [];

foreach ($months as $month) {
    $chartData[] = [
        'name' => $month,
        'Total' => 0
    ];
}

// Query the database for order totals per month
$sql = "SELECT DATE_FORMAT(o.created_at, '%M') AS month, SUM(oi.totalPrice) AS totalRevenue
        FROM orders o
        INNER JOIN order_items oi ON o.id = oi.orderId
        GROUP BY MONTH(o.created_at)
        ORDER BY MONTH(o.created_at)";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $month = $row['month'];
        $totalRevenue = (float) $row['totalRevenue'];

        // Update the chart data with the actual order totals
        foreach ($chartData as &$data) {
            if ($data['name'] === $month) {
                $data['Total'] = $totalRevenue;
                break;
            }
        }
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($chartData);
} else {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Failed to fetch chart data.']);
}

mysqli_close($conn);
?>
