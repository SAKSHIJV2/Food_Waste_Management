<?php
header("Content-Type: application/json");
session_start();
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['hotel_id'], $data['ngo_username'], $data['contact'], $data['food_id'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

$hotel_id = $data['hotel_id'];
$fullName = $data['ngo_username'];
$phoneNumber = $data['contact'];
$food_id = $data['food_id'];

// Get NGO id from user table
$stmt = $conn->prepare("SELECT id FROM user WHERE fullName = ? AND phoneNumber = ? AND userType = 'ngo'");
$stmt->bind_param("ss", $fullName, $phoneNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $ngo_id = $row['id'];

    // Insert into orders table
    $insert = $conn->prepare("INSERT INTO orders (hotel_id, ngo_id, food_id, status) VALUES (?, ?, ?, 'pending')");
    $insert->bind_param("iii", $hotel_id, $ngo_id, $food_id);

    if ($insert->execute()) {
        echo json_encode(["success" => true, "message" => "Order placed."]);
    } else {
        echo json_encode(["success" => false, "message" => $insert->error]);
    }

    $insert->close();
} else {
    echo json_encode(["success" => false, "message" => "NGO not found."]);
}

$stmt->close();
$conn->close();
