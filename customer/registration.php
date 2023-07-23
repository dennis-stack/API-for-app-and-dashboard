<?php
include '../connection.php';

// Set the response header to indicate JSON content
header('Content-Type: application/json');

// Get the POST data from the registration form
$data = json_decode(file_get_contents('php://input'), true);

$firstName = isset($data['firstName']) ? $data['firstName'] : '';
$lastName = isset($data['lastName']) ? $data['lastName'] : '';
$phoneNo = isset($data['phoneNo']) ? $data['phoneNo'] : '';
$email = isset($data['email']) ? $data['email'] : '';
$password = isset($data['password']) ? $data['password'] : '';

// Function to check if an email already exists in the database
function isEmailExists($email, $conn) {
    $query = "SELECT COUNT(*) AS count FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Function to check if an email is in the correct format
function isValidEmailFormat($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Check if the email already exists
if (isEmailExists($email, $conn)) {
    $response = array("success" => false, "message" => "Email already exists.");
    die(json_encode($response)); // Use die() to stop execution and return the response
}

// Check if the email is in the correct format
if (!isValidEmailFormat($email)) {
    $response = array("success" => false, "message" => "Invalid email format.");
    die(json_encode($response)); // Use die() to stop execution and return the response
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare and bind the parameters for the INSERT statement
$stmt = $conn->prepare("INSERT INTO users (firstName, lastName, phoneNo, email, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $firstName, $lastName, $phoneNo, $email, $hashedPassword);

// Execute the statement
if ($stmt->execute()) {
    $response = array("success" => true);
    echo json_encode($response);
} else {
    $response = array("success" => false, "message" => "Failed to register user.");
    echo json_encode($response);
}

$stmt->close();
$conn->close();

?>
