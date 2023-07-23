<?php
require_once('../connection.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    $response = array('success' => false, 'error' => 'not-logged-in');
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $response = array(
        'success' => true,
        'user' => array(
            'id' => $user['id'],
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'phoneNo' => $user['phoneNo'],
            'email' => $user['email']
        )
    );
    echo json_encode($response);
} else {
    $response = array('success' => false, 'error' => 'user-not-found');
    echo json_encode($response);
}

$conn->close();
?>
