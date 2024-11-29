<?php
require 'connection.php';

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['id']) || !is_numeric($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID provided']);
        exit;
    }

    $id = intval($data['id']);

    $stmt = $conn->prepare("DELETE FROM merchandise_sales WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
