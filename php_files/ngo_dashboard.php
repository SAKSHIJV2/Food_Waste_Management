<?php
// ngo_dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'ngo') {
    header("Location: login.php");
    exit();
}
echo "<h2>Welcome to the NGO Dashboard</h2>";
?>