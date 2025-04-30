<?php
session_start();


// Include database connection
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = trim($_POST['password']); // Do not hash here!
    
    // Fetch user from database
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $userType = $row['userType']; // Get user type
        
        // Compare the entered password with the hashed password
        if (password_verify($password, $hashed_password)) {
            
            echo "✅ Login successful!";
            
            // Redirect based on user type
            if ($userType === "hotel") {
                $_SESSION['hotel_id'] = $row['hotel_id'];
                header("Location: ../hotel/Hotelpage.php");
            } elseif ($userType === "ngo") {
                $_SESSION['ngo_username'] = $row['fullName']; // Adjust to your column name
                $_SESSION['ngo_contact'] = $row['phoneNumber']; 
                header("Location: ../ngo/ngo.html");
            } else {
                echo "❌ Unknown user type!";
            }
            exit(); // Ensure script stops after redirection
        } else {
            echo "❌ Incorrect password!";
        }
    } else {
        echo "❌ No user found with this email!";
    }

    $stmt->close();
    $conn->close();
}
?>
