<?php
require 'connection.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$firstName = $data['firstName'];
$lastName = $data['lastName'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);


$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User registered successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
