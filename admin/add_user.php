<?php
require_once "../connection.php";

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    // Return early for preflight requests
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Read the request body
    $data = json_decode(file_get_contents("php://input"), true);

    $firstName = $data["firstName"];
    $lastName = $data["lastName"];
    $phoneNo = $data["phoneNo"];
    $email = $data["email"];
    $password = $data["password"];

    if (empty($firstName) || empty($lastName) || empty($phoneNo) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["message" => "Please fill in all fields"]);
        exit();
    }

    // Validate the password length
    if (strlen($password) < 8) {
        // Return an error message if the password is too short
        http_response_code(400);
        echo json_encode(["message" => "Password should be at least 8 characters long"]);
        exit();
    }

    // Validate the phone number format
    if (!preg_match("/^07\d{8}$/", $phoneNo)) {
        // Return an error message if the phone number is invalid
        http_response_code(400);
        echo json_encode(["message" => "Phone number should start with 07 and be 10 digits long"]);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row["count"] > 0) {
        // Return an error message if the email already exists
        http_response_code(409);
        echo json_encode(["message" => "Email already exists"]);
        exit();
    }


    $query = "INSERT INTO users (firstName, lastName, phoneNo, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $firstName, $lastName, $phoneNo, $email, $hashedPassword);
    if ($stmt->execute()) {
        // User registered successfully
        http_response_code(200);
        echo json_encode(["message" => "User registered successfully"]);
    } else {
        // Return an error message if there was an issue with saving the user
        http_response_code(500);
        echo json_encode(["message" => "Error registering user"]);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Return an error for unsupported request methods
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
