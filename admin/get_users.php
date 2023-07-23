<?php
include '../connection.php';

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Content-Type");

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Fetch all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

if ($result) {
    $users = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    // Return the users as JSON response
    echo json_encode($users);
} else {
    $response = array("success" => false, "message" => "Failed to fetch users.");
    echo json_encode($response);
}

mysqli_close($conn);
?>
