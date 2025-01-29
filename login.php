<?php
$conn = new mysqli('localhost', 'root', '', 'smart_retail_db');

// Simulate receiving the UID from ESP32
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulating the card UID, but this should be sent by ESP32
    $uid = '6567e10'; // For testing, you can replace this with dynamic UID input

    if ($uid) {
        // Check if the UID exists in the database
        $query = "SELECT id, name FROM users WHERE rfid_uid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->bind_result($user_id, $user_name);
        $stmt->fetch();
        $stmt->close();

        if ($user_id) {
            // User is registered, start session and redirect to cart.php
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user_name;
            echo json_encode([
                "status" => "success",
                "message" => "Welcome, $user_name!",
                "name" => $user_name,
                "redirect" => "index.html"
            ]);
        } else {
            // UID not registered, prompt to register
            echo json_encode([
                "status" => "new_user",
                "message" => "New User Detected! Please Register.",
                "uid" => $uid,
                "redirect" => "register.html?uid=$uid"
            ]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "UID not detected."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
