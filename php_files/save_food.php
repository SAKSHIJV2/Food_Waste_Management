<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug - show received POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Ensure hotel_id is set in session
    $hotel_id = $_SESSION['hotel_id'] ?? null;
    if (!$hotel_id) {
        die("❌ Hotel ID not found. Please log in.");
    }

    // Validate if food_name and quantity are arrays
    if (!isset($_POST['food_name']) || !is_array($_POST['food_name']) || 
        !isset($_POST['quantity']) || !is_array($_POST['quantity'])) {
        die("❌ Invalid data format.");
    }

    $food_names = $_POST['food_name'];
    $quantities = $_POST['quantity'];
    $times = $_POST['time_left'] ?? [];
    $prices = $_POST['price'] ?? [];

    for ($i = 0; $i < count($food_names); $i++) {
        $food_name = trim($food_names[$i]);
        $quantity = trim($quantities[$i]);
        $time_left = isset($times[$i]) ? trim($times[$i]) : null;
        $price = isset($prices[$i]) ? trim($prices[$i]) : null;

        // Skip blank entries
        if (empty($food_name) || empty($quantity)) {
            continue;
        }

        // Prepare SQL query
        $sql = "INSERT INTO food_storage (hotel_id, category, food_item, quantity, time_left, price) 
                VALUES (?, 'Others', ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $hotel_id, $food_name, $quantity, $time_left, $price);
        $stmt->execute();
    }

    echo "<script>alert('✅ Data saved successfully!'); window.location.href = '../hotel/Hotelpage.php';</script>";
    exit();
}
?>
