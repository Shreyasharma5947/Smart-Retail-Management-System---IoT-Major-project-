<?php
$conn = new mysqli('localhost', 'root', '', 'rfid_loadcell_db');

$result = $conn->query("SELECT item_name, quantity, price, (quantity * price) AS total FROM cart WHERE quantity > 0");
$cart = [];

while ($row = $result->fetch_assoc()) {
    $cart[] = $row;
}

header('Content-Type: application/json');
echo json_encode($cart);

$conn->close();
?>
