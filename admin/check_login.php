<?php
session_start();

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Check if the user is logged in by checking the session or token
if (isset($_SESSION['admin_id'])) {
    // User is logged in, retrieve user details from the session or token
    $user = array(
        'id' => $_SESSION['admin_id'],
        'email' => $_SESSION['email'],
        'firstName' => $_SESSION['firstName'],
        'lastName' => $_SESSION['lastName']
        // Add any other necessary user details to the array
    );

    $response = array(
        'loggedIn' => true,
        'user' => $user
    );
} else {
    // User is not logged in
    $response = array(
        'loggedIn' => false
    );
}

echo json_encode($response);
?>
