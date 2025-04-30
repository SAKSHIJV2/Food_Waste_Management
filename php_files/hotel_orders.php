<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php';

$hotel_id = $_SESSION['hotel_id'] ?? null;

if (!$hotel_id) {
    echo "<div class='alert alert-danger'>Hotel not logged in!</div>";
    exit;
}

// Check if food is available
$checkFood = $conn->prepare("SELECT COUNT(*) as total FROM food_storage WHERE hotel_id = ?");
$checkFood->bind_param("i", $hotel_id);
$checkFood->execute();
$foodResult = $checkFood->get_result()->fetch_assoc();

if ($foodResult['total'] == 0) {
    echo "<div class='alert alert-success'>✅ All food orders completed. No active NGO orders to show.</div>";
    exit;
}

// Get NGO orders from 'orders' table
$stmt = $conn->prepare("
    SELECT DISTINCT u.fullName, u.phoneNumber 
    FROM orders o 
    JOIN user u ON o.ngo_id = u.id 
    WHERE o.hotel_id = ? AND o.status = 'pending'
");

$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>NGO Orders Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <h2>NGO Orders Received</h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-warning">No orders received yet.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>NGO Name</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fullName']) ?></td>
                        <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
