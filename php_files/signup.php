<?php
// Include database connection
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Check received POST data
    // echo "<pre>"; print_r($_POST); echo "</pre>";

    if (empty($_POST['user_type']) || empty($_POST['fullname']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['password'])) {
        die("❌ All fields are required.");
    }

    // Get input values
    $userType = mysqli_real_escape_string($conn, $_POST['user_type']);
    $fullName = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password

    // Check if email already exists
    $check_email_sql = "SELECT email FROM user WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("❌ Email already exists. Try logging in.");
    }
    $stmt->close();

    // Determine hotel_id only if the user is a hotel
    $hotelId = NULL;
    if ($userType === 'hotel') {
        $hotel_sql = "SELECT MAX(hotel_id) AS max_id FROM user WHERE userType = 'hotel'";
        $hotel_result = $conn->query($hotel_sql);
        $hotel_row = $hotel_result->fetch_assoc();
        $hotelId = $hotel_row['max_id'] + 1;
        if (!$hotelId) $hotelId = 1; // in case no hotel exists yet
    }

    // Insert user into database
    $sql = "INSERT INTO user (userType, fullName, phoneNumber, email, password, hotel_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ SQL Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssi", $userType, $fullName, $phoneNumber, $email, $password, $hotelId);

    if ($stmt->execute()) {
        echo "✅ Signup successful! Redirecting...";

        // Set session and redirect
        session_start();
        $_SESSION['userType'] = $userType;
        $_SESSION['fullName'] = $fullName;
        $_SESSION['hotel_id'] = $hotelId;

        if ($userType === "hotel") {
            header("Location: ../hotel/Hotelpage.php");
        } elseif ($userType === "ngo") {
            header("Location: ../ngo/ngo.html");
        } else {
            echo "❌ Unknown user type!";
        }
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    die("❌ Invalid request.");
}
?>
