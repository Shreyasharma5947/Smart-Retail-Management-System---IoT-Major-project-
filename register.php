<?php
$conn = new mysqli('localhost', 'root', '', 'smart_retail_db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data from the POST request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $rfid_uid = $_POST['rfid_uid'];

    // Insert the new user into the "users" table
    $query = "INSERT INTO users (name, email, phone, rfid_uid) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $email, $phone, $rfid_uid);
    
    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.html'>Go to login</a>";
    } else {
        echo "Registration failed. Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
