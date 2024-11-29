<?php
session_start();
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (empty($data['email']) || empty($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    $email = $data['email'];
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

  
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; 
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No account found with that email.']);
    }

    $stmt->close();
}
?>
