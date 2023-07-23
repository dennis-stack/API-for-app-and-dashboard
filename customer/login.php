<?php
require_once('../connection.php');

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

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // Start the session
        session_start();
    
        // Store user details in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        // Add any other necessary user details to the session
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['lastName'] = $user['lastName'];
        $_SESSION['phoneNo'] = $user['phoneNo'];
    
        // Prepare the response with user details
        $response = array(
            'success' => true,
            'user' => array(
                'firstName' => $user['firstName'],
                'lastName' => $user['lastName'],
                'phoneNo' => $user['phoneNo']
            )
        );

        echo json_encode($response);
    } else {
        $response = array('success' => false, 'error' => 'wrong-password');
        echo json_encode($response);
    }
} else {
    $response = array('success' => false, 'error' => 'user-not-found');
    echo json_encode($response);
}
?>
