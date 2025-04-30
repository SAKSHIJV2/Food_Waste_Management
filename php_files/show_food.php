<?php
session_start();
include '../php_files/db_connect.php';

if (!isset($_SESSION['hotel_id']) || empty($_SESSION['hotel_id'])) {
    die("❌ Please log in first. Hotel ID not set in session.");
}

$hotel_id = $_SESSION['hotel_id'];

$sql = "SELECT id, food_item, quantity, time_left, price FROM food_storage WHERE hotel_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Saved Food Items</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .food-table {
            width: 80%;
            margin: 50px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .food-table th, .food-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        .food-table th {
            background-color: #f8a100;
            color: white;
        }
        .food-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .food-table tr:hover {
            background-color: #f1f1f1;
        }
        .btn-complete {
            background-color: green;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-complete:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center;">Saved Food Items</h2>

    <table class="food-table">
        <tr>
            <th>Food Name</th>
            <th>Quantity</th>
            <th>Time Left</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['food_item']) ?></td>
            <td><?= htmlspecialchars($row['quantity']) ?></td>
            <td><?= htmlspecialchars($row['time_left']) ?></td>
            <td><?= htmlspecialchars($row['price']) ?></td>
            <td>
                <form method="POST" action="complete_food.php" onsubmit="return confirm('Are you sure this item is completed?');">
                    <input type="hidden" name="food_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="btn-complete">Complete</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

</body>
</html>
