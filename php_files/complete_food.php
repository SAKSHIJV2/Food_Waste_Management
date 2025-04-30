<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['hotel_id'])) {
    die("Unauthorized access.");
}

$hotel_id = $_SESSION['hotel_id'];
$food_id = $_POST['food_id'] ?? '';

if (!empty($food_id)) {
    $stmt = $conn->prepare("DELETE FROM food_storage WHERE id = ? AND hotel_id = ?");
    $stmt->bind_param("ii", $food_id, $hotel_id);
    if ($stmt->execute()) {
        header("Location: show_food.php"); // Refresh the page
        exit();
    } else {
        echo "❌ Error deleting item.";
    }
} else {
    echo "❌ Invalid request.";
}
?>
