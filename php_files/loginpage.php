<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <style>
        body{
            margin: 0;
            padding: 0;
            background-image: url("https://r2.erweima.ai/imgcompressed/compressed_48f439c24f2c9436ff4bc185cefd30ff.webp");
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            height: 100vh; 
            width: 100vw; 
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }
        .box{
            height: 450px;
            width: 350px;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-in-out;
            margin-top: 80px;
            margin-left: 250px;
        }
        .box h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 35px;
        }
        .input-box {
            width: 90%;
            margin-bottom: 15px;
            height: 8%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background: #f3f3f3;
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease-in-out;
        }
        .input-box:focus {
            outline: none;
            background: white;
            box-shadow: inset 3px 3px 8px rgba(0, 0, 0, 0.2);
        }
        .login-btn {
            width: 95%;
            padding: 12px;
            font-size: 18px;
            color: white;
            background: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }
        .login-btn:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .links {
            margin-top: 15px;
            font-size: 14px;
            cursor: pointer;
        }

        .links a {
            text-decoration: none;
            color: #007BFF;
            transition: 0.3s ease-in-out;
        }

        .links a:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hides the box initially */
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

    <div class="box" id="loginBox">
        <h2>Login</h2>
        
        <form action="login.php" method="POST" >
            <!-- Dropdown to Select User Type -->
            <select name="user_type" class="input-box" required>
                <option value="hotel">Hotel</option>
                <option value="ngo">NGO</option>
            </select>
            
            <input type="email" name="email" class="input-box" placeholder="Email" required>
            <input type="password" name="password" class="input-box" placeholder="Password" required>
            <button type="submit" class="login-btn">Login</button>
            
            <div class="links"> 
                <a href="#">Forgot Password?</a> | 
                <a onclick="showSignup()">New user? Sign Up</a>
            </div>
        </form>
    </div>


    <div class="box hidden" id="signupBox">
        <h2>Sign Up</h2>

        <form action="signup.php" method="POST">
            <!-- Dropdown to Select User Type -->
            <select name="user_type" class="input-box" required>
                <option value="hotel">Hotel</option>
                <option value="ngo">NGO</option>
            </select>

            <input type="text" name="fullname" class="input-box" placeholder="Full Name" required>
            <input type="email" name="email" class="input-box" placeholder="Email" required>
            <input type="tel" name="phone" class="input-box" placeholder="Enter Phone Number" pattern="[0-9]{10}" maxlength="10" required>
            <input type="password" name="password" class="input-box" placeholder="Create Password" required>
            
            <button type="submit" class="login-btn">Sign Up</button>

            <div class="links">
                <a onclick="showLogin()">Already have an account? Login</a>
            </div>
        </form>
    </div>


    <script>
        function showSignup() {
            document.getElementById("loginBox").classList.add("hidden");
            document.getElementById("signupBox").classList.remove("hidden");
        }

        function showLogin() {
            document.getElementById("signupBox").classList.add("hidden");
            document.getElementById("loginBox").classList.remove("hidden");
        }

        // Handle login button click
        function handleLogin() {
            const userType = document.getElementById("userType").value; // Get user type (Hotel/NGO)
            const username = document.querySelector("input[type='text']").value;
            const password = document.querySelector("input[type='password']").value;

            // Validate credentials (you can add your own validation logic here)

            // Redirect based on user type
            if (userType === 'hotel') {
                window.location.href = 'hotel_dashboard.php'; // Redirect to Hotel dashboard
            } else if (userType === 'ngo') {
                window.location.href = 'ngo_dashboard.php'; // Redirect to NGO dashboard
            }
        }

        // Handle signup button click
        function handleSignup() {
            // Add your signup logic here (you can use AJAX to handle the signup without page reload)
            alert("Signup functionality to be implemented.");
        }
    </script>

</body>
</html>
