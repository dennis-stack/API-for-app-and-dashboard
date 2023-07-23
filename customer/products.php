<?php
require_once('../connection.php');

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Fetch products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    $products = array();

    // Fetch each row from the result as an associative array
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    // Return the products as JSON response
    echo json_encode($products);
} else {
    // Return an error message if the query failed
    $response = array(
        'error' => 'Failed to fetch products.'
    );
    echo json_encode($response);
}

// Close the database connection
mysqli_close($conn);
?>
