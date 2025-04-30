<?php
session_start();
include 'db_connect.php';

$ngo_id = $_SESSION['user_id'] ?? null; // assuming NGO login sets this
$food_id = $_POST['food_id'] ?? null;

if (!$ngo_id || !$food_id) {
    echo "Invalid request!";
    exit;
}

// Get the hotel_id from the selected food item
$getHotel = $conn->prepare("SELECT hotel_id FROM food_storage WHERE id = ?");
$getHotel->bind_param("i", $food_id);
$getHotel->execute();
$getHotelResult = $getHotel->get_result()->fetch_assoc();

$hotel_id = $getHotelResult['hotel_id'] ?? null;

if (!$hotel_id) {
    echo "Hotel not found for this food item.";
    exit;
}

// Insert order into the database
$insertOrder = $conn->prepare("INSERT INTO orders (hotel_id, ngo_id, food_id) VALUES (?, ?, ?)");
$insertOrder->bind_param("iii", $hotel_id, $ngo_id, $food_id);

if ($insertOrder->execute()) {
    echo "<script>alert('Order Sent!'); window.location.href='foodchart.php';</script>";
} else {
    echo "Failed to send order!";
}
?>
