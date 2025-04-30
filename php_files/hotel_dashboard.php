<?php
// hotel_dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'hotel') {
    header("Location: login.php");
    exit();
}
echo "<h2>Welcome to the Hotel Dashboard</h2>";
?>