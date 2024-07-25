<?php
// Enable error reporting for all errors and log them to a file
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'errors.log'); // Specify the path to your error log file

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error); // Log error
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $query = $_POST['query'];
    $priority = $_POST['priority'];
    $location = $_POST['location'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO ticket_quries (name, email, query, location, priority) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $conn->error); // Log error
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $email, $query, $location, $priority);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        error_log("Error executing statement: " . $stmt->error); // Log error
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
