<?php
include 'db_connect.php';

// Set response type to JSON
header('Content-Type: application/json');

// Get hotel_id from the query string
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;

if ($hotel_id === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid hotel ID']);
    exit;
}

// Prepare and execute the query
$stmt = $conn->prepare("SELECT food_item, quantity, time_left FROM food_storage WHERE hotel_id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch results
$food_chart = [];
while ($row = $result->fetch_assoc()) {
    $food_chart[] = [
        'food_name' => $row['food_item'],  // keeping frontend structure
        'quantity' => $row['quantity'],
        'time' => $row['time_left']
    ];
}

// Output as JSON
echo json_encode($food_chart);

// Close DB connection
$stmt->close();
$conn->close();
