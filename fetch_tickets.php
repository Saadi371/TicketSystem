<?php
// fetch_tickets.php

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query and page number from URL parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10; // Number of items per page
$offset = ($page - 1) * $itemsPerPage;

// SQL query to get the total number of tickets
$totalQuery = "SELECT COUNT(*) as total FROM `ticket_quries` 
               WHERE `name` LIKE '%$search%' 
               OR `email` LIKE '%$search%' 
               OR `query` LIKE '%$search%' 
               OR `location` LIKE '%$search%' 
               OR `priority` LIKE '%$search%' 
               OR `Status` LIKE '%$search%'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalTickets = $totalRow['total'];

// SQL query to fetch data with optional search filtering and pagination
$sql = "SELECT * FROM `ticket_quries` 
        WHERE `name` LIKE '%$search%' 
        OR `email` LIKE '%$search%' 
        OR `query` LIKE '%$search%' 
        OR `location` LIKE '%$search%' 
        OR `priority` LIKE '%$search%' 
        OR `Status` LIKE '%$search%'
        OR `attended_by` LIKE '%$search%'
        ORDER BY `id` DESC 
        LIMIT $itemsPerPage OFFSET $offset";

$result = $conn->query($sql);

$tickets = '';
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $tickets .= "<tr>";
        $tickets .= "<td>" . $row["name"] . "</td>";
        $tickets .= "<td>" . $row["email"] . "</td>";
        $tickets .= "<td>" . $row["query"] . "</td>";
        $tickets .= "<td>" . $row["location"] . "</td>";
        if ($row["priority"] == 'Low') {
            $tickets .= "<td><span style='background-color: blue; color: white; padding: 3px 7px; border-radius: 3px;'>" . $row["priority"] . "</span></td>";
        }
        if ($row["priority"] == 'Medium') {
            $tickets .= "<td><span style='background-color: yellow; color: black; padding: 3px 7px; border-radius: 3px;'>" . $row["priority"] . "</span></td>";
        }
        if ($row["priority"] == 'High') {
            $tickets .= "<td><span style='background-color: red; color: white; padding: 3px 7px; border-radius: 3px;'>" . $row["priority"] . "</span></td>";
        }
        
        $tickets .= "<td>" . $row["time"] . "</td>";
        $tickets .= "<td>";
        $selectClass = $row["Status"] == 'Completed' ? 'status-completed' : '';
        $tickets .= "<select class='form-control status-dropdown $selectClass' data-ticket-id='" . $row["id"] . "'>";
        $tickets .= "<option value='unattended' " . ($row["Status"] == 'unattended' ? 'selected' : '') . ">unattended</option>";
        $tickets .= "<option value='Completed' " . ($row["Status"] == 'Completed' ? 'selected' : '') . ">Completed</option>";
        $tickets .= "</select>";
        $tickets .= "</td>";
        $tickets .= "<td>" . $row["attended_by"] . "</td>";
        $tickets .= "</tr>";
    }
} else {
    $tickets .= "<tr><td colspan='8'>No results found</td></tr>";
}

echo json_encode(['tickets' => $tickets, 'total' => $totalTickets]);

$conn->close();
?>
