<?php
require_once('../connection.php');

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Check if the category parameter is provided in the request
if (isset($_GET['productType'])) {
    // Sanitize and escape the category value
    $category = mysqli_real_escape_string($conn, $_GET['productType']);

    // Fetch products from the database for the specified category
    $query = "SELECT * FROM products WHERE productType = '$category'";
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
            'error' => 'Failed to fetch products for the specified category.'
        );
        echo json_encode($response);
    }
} else {
    // Return an error message if the category parameter is missing
    $response = array(
        'error' => 'Category parameter is required.'
    );
    echo json_encode($response);
}

// Close the database connection
mysqli_close($conn);
?>
