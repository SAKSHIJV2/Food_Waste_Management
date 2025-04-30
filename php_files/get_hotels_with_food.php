<?php
include 'db_connect.php';

// Set response type to JSON
header('Content-Type: application/json');

// Prepare SQL
$sql = "SELECT DISTINCT u.fullName, u.hotel_id 
        FROM user u
        JOIN food_storage f ON u.hotel_id = f.hotel_id 
        WHERE u.userType = 'hotel' AND f.hotel_id IS NOT NULL";

// Execute query
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    http_response_code(500);
    exit;
}

// Fetch data
$hotels = [];
while ($row = $result->fetch_assoc()) {
    $hotels[] = $row;
}

// Return JSON
echo json_encode($hotels);

// Close DB connection
$conn->close();
?>
