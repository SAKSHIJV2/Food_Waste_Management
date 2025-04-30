<?php
include 'db_connect.php';
session_start();

$hotel_id = $_SESSION['hotel_id']; // assuming hotel ID is saved in session

$sql = "SELECT ngo_username, contact FROM orders WHERE hotel_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();

$result = $stmt->get_result();
$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
