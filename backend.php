<?php
// Set headers for CORS and JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'rfid_loadcell_db');

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Predefined prices for items
$prices = [
    "Water Bottle" => 100,
    "Lunch Box" => 200,
];

// Handle POST for pickup/drop actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'] ?? null;
    $action = $_POST['action'] ?? null;

    if (empty($product_name) || empty($action)) {
        echo json_encode(["status" => "error", "message" => "Missing product name or action"]);
        exit;
    }

    $price = $prices[$product_name] ?? 0;

    if ($action === 'pickup') {
        $stmt = $conn->prepare("INSERT INTO cart (item_name, quantity, price) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->bind_param("si", $product_name, $price);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "$product_name added to cart"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error executing pickup action"]);
        }
    } elseif ($action === 'drop') {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE item_name = ? AND quantity > 0");
        $stmt->bind_param("s", $product_name);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "$product_name removed from cart"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No rows updated, either product not found or quantity is zero"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle GET request for cart data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT item_name, quantity, price, (quantity * price) AS total FROM cart WHERE quantity > 0");
    $cart = [];

    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }

    echo json_encode($cart);
    $conn->close();
    exit;
}

// Invalid request method
echo json_encode(["status" => "error", "message" => "Invalid request method"]);
$conn->close();
?>
