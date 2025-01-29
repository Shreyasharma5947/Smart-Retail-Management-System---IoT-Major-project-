<?php
// Set headers for CORS and JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'rfid_loadcell_db');

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Predefined UID to Product Mapping
$predefinedProducts = [
    '6567e10' => 'Water Bottle',
    '9b7bb013' => 'Lunch Box',
];

// Handle POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'] ?? null;

    if (!empty($uid)) {
        // Check if UID exists in database
        $stmt = $conn->prepare("SELECT product_name FROM products WHERE rfid_uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->bind_result($product_name);
        $stmt->fetch();
        $stmt->close();

        if ($product_name) {
            echo json_encode(["status" => "success", "message" => "Product recognized!", "product" => $product_name]);
        } else {
            $productName = $predefinedProducts[$uid] ?? "Unknown Product";
            $stmt = $conn->prepare("INSERT INTO products (product_name, rfid_uid) VALUES (?, ?)");
            $stmt->bind_param("ss", $productName, $uid);
            $stmt->execute();
            $stmt->close();

            echo json_encode([
                "status" => $productName === "Unknown Product" ? "error" : "success",
                "message" => $productName === "Unknown Product"
                    ? "No product found. UID $uid added as 'Unknown Product'."
                    : "UID $uid recognized as $productName.",
                "product" => $productName
            ]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No UID provided"]);
    }
    exit;
}

// Handle GET Request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT product_name FROM products WHERE product_name != 'Unknown Product'");
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row['product_name'];
    }

    echo json_encode(["status" => "success", "products" => $products]);
    exit;
}

// Invalid Request Method
echo json_encode(["status" => "error", "message" => "Invalid request method"]);
$conn->close();
?>
