<?php
session_start();
include '../php_files/db_connect.php';

// Get hotel_id from session
$hotel_id = $_SESSION['hotel_id'];

// Fetch latest order for this hotel
$sql = "SELECT fullName, phoneNumber FROM user WHERE hotel_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$ngo = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Storage Chart</title>
    
    <style>
       /* General Body Styling */
        body {
            font-family: 'Poppins', sans-serif;           
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;  /* Changed from center to flex-start */
            min-height: 100vh;  /* Ensures page extends to fit content */
            overflow: auto;  /* Enables scrolling if content overflows */
            background-color: #FBFBFB;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #333;
            color: white;
            position: fixed;
            left: -250px; /* Sidebar starts hidden */
            top: 0;
            transition: left 0.3s ease-in-out;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar h2 {
            font-size: 28px;  /* Increase the size */
            font-weight: bold; /* Make it bold */
            text-align: center; /* Center align */
            color: #ffcc00; /* Highlighted color */
            text-transform: uppercase; /* Make it all caps */
            padding: 20px 0; /* Add spacing around */
            background: #222; /* Dark background */
            border-radius: 5px; /* Smooth edges */
        }

        .sidebar ul li {
            margin: 10px 0;
        }

        .sidebar ul li a {
            display: block;
            background: #444; /* Dark gray for contrast */
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            transition: background 0.3s, transform 0.2s;
        }

        .sidebar ul li a:hover {
            background: #666; /* Lighter gray on hover */
            transform: scale(1.05); /* Slight enlargement */
        }

        /* Active/Selected Link */
        .sidebar ul li a.active {
            background: #ff4d4d; /* Highlight color */
            font-weight: bold;
        }

        /* Sidebar List */
        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            transition: color 0.3s ease-in-out;
        }

        .sidebar ul li a:hover {
            color: #f1c40f;
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 20px;
            padding: 20px;
        }

        /* Toggle Button */
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            font-size: 24px;
            background: white; /* Background for contrast */
            border: 1px solid #333;
            padding: 10px 15px;
            border-radius: 5px;
            color: black;
            cursor: pointer;
            z-index: 1100; /* Ensure it stays on top */
            transition: background 0.3s ease-in-out;
        }
        .toggle-btn:hover {
            background: #ddd; /* Hover effect */
        }

        /* Close Button (inside Sidebar) */
        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }
        /* Main Container */
        .food-container {
            background: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.2); /* Slightly stronger background */
            padding: 30px; /* More padding for better spacing */
            width: 60%; /* Slightly wider for a balanced layout */
            max-width: 80%; 
             /* Maintain content width */
            border-radius: 20px; /* Smoother rounded corners */
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25); /* Stronger shadow for depth */
            backdrop-filter: blur(12px); /* Stronger glassmorphism effect */
            text-align: center; /* Center content */
            animation: fadeIn 1s ease-in-out, popIn 0.5s ease-in-out 1s; /* Smooth animations */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Hover effect */

            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .food-container:hover {
            transform: scale(1.02); /* Slight scaling */
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); /* Enhanced shadow */
        }

        /* General Category Box Styling */
        .category-box {
            font-size: 20px;
            font-weight: bold;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            border-radius: 12px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s;
        }
        .food-list {
            list-style: none;
            padding: 0;
        }

        .item-box {
            background: #f4f4f4;
            padding: 15px;
            margin: 5px 0;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            display: flex;
            align-items: center;
        }

        .arrow {
            margin-right: 10px;
        }

        .sub-list {
            list-style: none;
            padding-left: 20px;
            display: none; /* Initially hidden */
        }

        .extra-info {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin-top: 5px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .input-box {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Unique Colors for Each Category */
        .perishable-box {
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            border: 2px solid #ff4b5c;
        }

        .semi-perishable-box {
            background: linear-gradient(135deg, #ffb84d, #ffcc80);
            border: 2px solid #ff9900;
        }

        .non-perishable-box {
            background: linear-gradient(135deg, #4db8ff, #80ccff);
            border: 2px solid #0099ff;
        }

        /* Hover Effect */
        .category-box:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }


        /* Title & Headings */
        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
        }

        h2 {
            color: #34495e;
            margin-top: 20px;
            font-size: 22px;
            border-bottom: 2px solid #34495e;
            display: inline-block;
            padding-bottom: 5px;
        }

        /* Food List Styling */
        .food-list {
            list-style: none;
            padding-left: 0;
            text-align: left;
        }

        /* Item Box Styling */
        .item-box {
            font-size: 16px;
            padding: 10px;
            margin: 5px 10px;
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            transition: transform 0.3s ease-in-out, background 0.3s;
        }

        /* Hover Effects */
        .item-box:hover {
            background: #ecf0f1;
            transform: scale(1.05);
        }
        .arrow {
            font-size: 18px;
            transition: transform 0.3s ease-in-out;
        }

        /* Extra Info (Initially Hidden) */
        .extra-info {
            display: none;
            background: #fff8e1;
            padding: 10px;
            margin: 5px 15px;
            border-radius: 6px;
            border-left: 4px solid #e67e22;
            font-size: 14px;
        }

        /* Input Boxes */
        .input-box {
            width: 90%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            display: block;
        }
        .spacer {
            height: 20px; /* Adds vertical space before "Others" */
        }

        .others-item {
            margin-top: 15px; /* Space above "Others" option */
            font-weight: bold; /* Make it stand out */
            background: rgba(255, 255, 255, 0.2); /* Subtle highlight */
            border-radius: 10px; /* Smooth corners */
            padding: 10px;
        }

        .extra-info {
            margin-top: 10px; /* Space between input fields */
            padding: 10px;
            border-left: 3px solid #007bff; /* Blue left border for emphasis */
        }
        .save-btn {
            display: block;
            width: 180px;  /* Slightly bigger button */
            padding: 12px;
            margin: 40px auto; /* More spacing from above content */
            background-color: #007bff; /* Blue color */
            color: white;
            border: none;
            border-radius: 10px; /* Smoother edges */
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
        }

        /* Hover Effect */
        .save-btn:hover {
            background-color: #0056b3; /* Darker blue */
            transform: scale(1.05); /* Slightly enlarges on hover */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3); /* More depth */
        }
        /* Fade-in Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes popIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <h2>Annseva</h2>
        <ul>
            <li><a href="landingpage.html">Home</a></li>
            <li><a href="../php_files/show_food.php">Profile</a></li>
            <li><a href="../php_files/hotel_orders.php"">Orders</a></li> 
            <li><a href="#">Settings</a></li>
            <li><a href="../php_files/logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>
    <!-- Button to Toggle Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    
    <div class="food-container">
        <h1>Food Storage Chart</h1>
        <form action="/AnnSeva/php_files/save_food.php" method="POST">
        <h2 class="category-box perishable-box">Perishable Food</h2>
        <ul class="food-list">
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Milk</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Milk">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Cut/Fresh Fruits and Vegetables</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Cut/Fresh Fruits and Vegetables">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Yogurt</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Yogurt">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Cheese</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Cheese">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Meat and Poultry</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Meat and Poultry">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Fish</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Fish">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Eggs</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Eggs">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            
            <ul class="food-list">
                <li class="item-box" onclick="toggleSubList(this)">
                    <span class="arrow">▶</span> <span>Cooked Food</span>
                </li>
            <ul class="sub-list">
                <!-- Chapati -->
                <li class="item-box" onclick="toggleInfo(this)">
                    <span class="arrow">▶</span> <span>Chapati</span>
                </li>
                <div class="extra-info">
                    <input type="hidden" name="food_name[]" value="Chapati">
                    <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                    <label>Quantity:</label>
                    <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 4 pieces)">
                    
                    <label>How many hours/days it has been left?</label>
                    <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">
        
                    <label>Price of the donated item (approx)?</label>
                    <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $1)">
                </div>
        
                <!-- Rice -->
                <li class="item-box" onclick="toggleInfo(this)">
                    <span class="arrow">▶</span> <span>Rice</span>
                </li>
                <div class="extra-info">
                    <input type="hidden" name="food_name[]" value="Rice">
                    <p>Stored for 6 hours at room temp, 2 days in fridge.</p>
                    <label>Quantity:</label>
                    <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 kg)">
                    
                    <label>How many hours/days it has been left?</label>
                    <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 8 hours)">
        
                    <label>Price of the donated item (approx)?</label>
                    <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $3)">
                </div>
        
                <!-- Daal -->
                <li class="item-box" onclick="toggleInfo(this)">
                    <span class="arrow">▶</span> <span>Daal</span>
                </li>
                <div class="extra-info">
                    <input type="hidden" name="food_name[]" value="Daal">
                    <p>Stored for 12 hours at room temp, 2 days in fridge.</p>
                    <label>Quantity:</label>
                    <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 500 ml)">
                    
                    <label>How many hours/days it has been left?</label>
                    <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 6 hours)">
        
                    <label>Price of the donated item (approx)?</label>
                    <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
                </div>
        
                <!-- Curry -->
                <li class="item-box" onclick="toggleInfo(this)">
                    <span class="arrow">▶</span> <span>Curry</span>
                </li>
                <div class="extra-info">
                    <input type="hidden" name="food_name[]" value="Curry">
                    <p>Stored for 12 hours at room temp, 1-2 days in fridge.</p>
                    <label>Quantity:</label>
                    <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 500 ml)">
                    
                    <label>How many hours/days it has been left?</label>
                    <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 4 hours)">
        
                    <label>Price of the donated item (approx)?</label>
                    <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
                </div>
                <!-- Others Option -->
                <li class="item-box" onclick="toggleInfo(this)">
                    <span class="arrow">▶</span> <span>Others</span>
                </li>
                <div class="extra-info">
                    <label>Enter Food Name:</label>
                    <input type="text" class="input-box" name="food_name[]" placeholder="Enter food name (e.g., Pasta)">

                    <label>Quantity:</label>
                    <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 500 ml)">
                    
                    <label>How many hours/days it has been left?</label>
                    <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 4 hours)">

                    <label>Price of the donated item (approx)?</label>
                    <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
                </div>
            </ul>
        </ul>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Milk (UHT)</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Milk(UHT)">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Flour</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Flour">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Grain</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Grain">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
        </ul>

        <h2 class="category-box semi-perishable-box">Semi-Perishable Food</h2>
        <ul class="food-list">
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Potatoes</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Potatoes">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Onion</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Onion">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Garlic</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Garlic">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Ginger</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Ginger">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <ul class="food-list">
                <!-- Baked Foods Main Category -->
                <li class="item-box" onclick="toggleSubList(this)">
                    <span class="arrow">▶</span> <span>Baked Foods</span>
                </li>
                <ul class="sub-list">
                    <!-- Pav -->
                    <li class="item-box" onclick="toggleInfo(this)">
                        <span class="arrow">▶</span> <span>Pav</span>
                    </li>
                    <div class="extra-info">
                        <input type="hidden" name="food_name[]" value="Pav">
                        <p>Stored for 12 hours at room temp, 2-3 days in fridge.</p>
                        <label>Quantity:</label>
                        <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 6 pieces)">
                        
                        <label>How many hours/days it has been left?</label>
                        <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 8 hours)">
            
                        <label>Price of the donated item (approx)?</label>
                        <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
                    </div>
            
                    <!-- Bread -->
                    <li class="item-box" onclick="toggleInfo(this)">
                        <span class="arrow">▶</span> <span>Bread</span>
                    </li>
                    <div class="extra-info">
                        <input type="hidden" name="food_name[]" value="Bread">
                        <p>Stored for 24 hours at room temp, 3-5 days in fridge.</p>
                        <label>Quantity:</label>
                        <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 loaf)">
                        
                        <label>How many hours/days it has been left?</label>
                        <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 1 day)">
            
                        <label>Price of the donated item (approx)?</label>
                        <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $3)">
                    </div>
            
                    <!-- Donuts -->
                    <li class="item-box" onclick="toggleInfo(this)">
                        <span class="arrow">▶</span> <span>Donuts</span>
                    </li>
                    <div class="extra-info">
                        <input type="hidden" name="food_name[]" value="Donuts">
                        <p>Stored for 6-12 hours at room temp, 2 days in fridge.</p>
                        <label>Quantity:</label>
                        <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 4 pieces)">
                        
                        <label>How many hours/days it has been left?</label>
                        <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">
            
                        <label>Price of the donated item (approx)?</label>
                        <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $5)">
                    </div>
            
                    <!-- Cakes -->
                    <li class="item-box" onclick="toggleInfo(this)">
                        <span class="arrow">▶</span> <span>Cakes</span>
                    </li>
                    <div class="extra-info">
                        <input type="hidden" name="food_name[]" value="Cakes">
                        <p>Stored for 12 hours at room temp, 3-5 days in fridge.</p>
                        <label>Quantity:</label>
                        <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 500g)">
                        
                        <label>How many hours/days it has been left?</label>
                        <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 6 hours)">
            
                        <label>Price of the donated item (approx)?</label>
                        <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $10)">
                    </div>
            
                    <!-- Others Option -->
                    <li class="item-box" onclick="toggleInfo(this)">
                        <span class="arrow">▶</span> <span>Others</span>
                    </li>
                    <div class="extra-info">
                        <label>Enter Food Name:</label>
                        <input type="text" class="input-box" name="food_name[]" placeholder="Enter food name (e.g., Pastry)">
            
                        <label>Quantity:</label>
                        <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 500g)">
                        
                        <label>How many hours/days it has been left?</label>
                        <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 4 hours)">
            
                        <label>Price of the donated item (approx)?</label>
                        <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $3)">
                    </div>
                </ul>
            </ul>
            
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Dry Fruits</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Dry Fruits">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Sugar</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Sugar">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
        </ul>

        <h2 class="category-box non-perishable-box">Non-Perishable Food</h2>
        <ul class="food-list">
            
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Dried Beans</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Dried Beans">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Spices</span> </li>
            <div class="extra-info">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Enter Name:</label>
            <input type="text" class="input-box" name="food_name[]" value="Spices" placeholder="Enter food name (e.g., haldi)">
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Canned Foods</span> </li>
            <div class="extra-info">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Enter Food Name:</label>
                <input type="text" class="input-box" name="food_name[]" value="Canned Foods" placeholder="Enter food name (e.g., Rasgulla)">
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
            <li class="item-box" onclick="toggleInfo(this)"><span class="arrow">▶</span><span>   Salt</span> </li>
            <div class="extra-info">
                <input type="hidden" name="food_name[]" value="Salt">
                <p>Stored for 24 hours at room temp, 1-2 days in fridge.</p>
                <label>Quantity:</label>
                <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 1 liter)">
                
                <label>How many hours/days it has been left?</label>
                <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 5 hours)">

                <label>Price of the donated item (approx)?</label>
                <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $2)">
            </div>
        </ul>

        <!-- Other food items here -->

        <!-- Add spacing before the "Others" option -->
        <li class="spacer"></li>  <!-- Empty spacer for extra space -->
        <li class="item-box others-item" onclick="toggleInfo(this)">
            <span class="arrow">▶</span> <span>Others</span>
        </li>
        <div class="extra-info">
            <label>Enter Food Name:</label>
            <input type="text" class="input-box" name="food_name[]" placeholder="Enter food name (e.g., Dosa)">

            <label>Quantity:</label>
            <input type="text" class="input-box" name="quantity[]" placeholder="Enter quantity (e.g., 500g)">
            
            <label>How many hours/days it has been left?</label>
            <input type="text" class="input-box" name="time_left[]" placeholder="Enter hours/days (e.g., 4 hours)">

            <label>Price of the donated item (approx)?</label>
            <input type="text" class="input-box" name="price[]" placeholder="Enter price (e.g., $3)">
        </div>
        <!-- Save Button at the Bottom -->
            <button class="save-btn">Save</button>
        </form>
    </div>
    

    <script>
        function toggleInfo(item) {
            let extraInfo = item.nextElementSibling;
            let arrow = item.querySelector('.arrow');

            if (extraInfo.style.display === "block") {
                extraInfo.style.display = "none";
                arrow.innerHTML = "▶"; // Collapse icon
            } else {
                extraInfo.style.display = "block";
                arrow.innerHTML = "▼"; // Expand icon
            }
        }
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let overlay = document.getElementById("overlay");

            if (sidebar.style.left === "0px") {
                sidebar.style.left = "-250px"; // Hide sidebar
                overlay.style.display = "none"; // Hide overlay
            } else {
                sidebar.style.left = "0px"; // Show sidebar
                overlay.style.display = "block"; // Show overlay
            }
        }
        function toggleSubList(element) {
            let subList = element.nextElementSibling;
            let arrow = element.querySelector(".arrow");

            if (subList.style.display === "block") {
                subList.style.display = "none";
                arrow.innerHTML = "▶"; // Collapsed
            } else {
                subList.style.display = "block";
                arrow.innerHTML = "▼"; // Expanded
            }
        }

        function toggleInfo(element) {
            let infoDiv = element.nextElementSibling;
            let arrow = element.querySelector(".arrow");

            if (infoDiv.style.display === "block") {
                infoDiv.style.display = "none";
                arrow.innerHTML = "▶"; // Collapsed
            } else {
                infoDiv.style.display = "block";
                arrow.innerHTML = "▼"; // Expanded
            }
        }

    </script>
</body>
</html>
