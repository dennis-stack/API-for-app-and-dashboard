<?php
require_once('../connection.php');

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$jsonData = json_decode(file_get_contents('php://input'), true);

$email = $jsonData['email'];
$password = $jsonData['password'];

if (empty($email) || empty($password)) {
    $response = array('success' => false, 'error' => 'empty-fields');
    echo json_encode($response);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = array('success' => false, 'error' => 'invalid-email');
    echo json_encode($response);
    exit();
}

$sql = "SELECT * FROM admin WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $storedPassword = $admin['password'];

    // Compare the entered password with the stored password
    if ($password === $storedPassword) {
        // Start the session
        session_start();

        // Store admin details in the session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['email'] = $admin['email'];
        // Add any other necessary admin details to the session
        $_SESSION['firstName'] = $admin['firstName'];
        $_SESSION['lastName'] = $admin['lastName'];

        // Prepare the response with admin details
        $response = array(
            'success' => true,
            'admin' => array(
                'firstName' => $admin['firstName'],
                'lastName' => $admin['lastName']
            )
        );

        echo json_encode($response);
    } else {
        $response = array('success' => false, 'error' => 'wrong-password');
        echo json_encode($response);
    }
} else {
    $response = array('success' => false, 'error' => 'admin-not-found');
    echo json_encode($response);
}
?>
