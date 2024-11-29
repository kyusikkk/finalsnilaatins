<?php
require 'connection.php';

$result_array = array();

$sql = "SELECT id, item, description, category, list_price, sale_price, quantity, total_value FROM merchandise_sales";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $result_array[] = $row;
    }
}


header('Content-Type: application/json');
echo json_encode($result_array);

$conn->close();
?>
