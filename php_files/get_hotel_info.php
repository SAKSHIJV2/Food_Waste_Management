<?php
include 'db_connect.php';

if (isset($_GET['hotel_id'])) {
    $hotel_id = $_GET['hotel_id'];

    $stmt = $conn->prepare("SELECT fullName, phoneNumber FROM user WHERE hotel_id = ? AND userType = 'hotel'");
    $stmt->bind_param("s", $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $hotel = $result->fetch_assoc();
    echo json_encode($hotel);
}
?>
