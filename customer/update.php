<?php
require_once('../connection.php');

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$phoneNo = $_POST['phoneNo'];
$email = $_POST['email'];
$password = $_POST['password'];

if (empty($firstName) || empty($lastName) || empty($phoneNo) || empty($email)) {
    $response = array('success' => false, 'message' => 'Please fill all fields');
    echo json_encode($response);
    exit;
}

$query = "UPDATE users SET firstName = ?, lastName = ?, phoneNo = ?";
$params = array($firstName, $lastName, $phoneNo);

// Check if the password field is not empty
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password = ?";
    $params[] = $hashedPassword;
}

$query .= " WHERE email = ?";
$params[] = $email;

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$result = $stmt->execute();

if ($result) {
    $response = array('success' => true, 'message' => 'User details updated successfully');
    echo json_encode($response);
} else {
    $response = array('success' => false, 'message' => 'Failed to update user details');
    echo json_encode($response);
}

$stmt->close();
?>
