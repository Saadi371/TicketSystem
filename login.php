<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "ticket_system";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validate email and password
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute() === false) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Valid credentials
        $_SESSION['username'] = $username;
        echo 'success';
    } else {
        // Invalid credentials
        echo 'Invalid username or password.';
    }

    $stmt->close();
    $conn->close();
}
?>
