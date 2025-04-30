<?php
session_start();
$ngo_username = $_SESSION['ngo_username'] ?? '';
$ngo_contact = $_SESSION['ngo_contact'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Chart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container1 {
            display: flex;
            gap: 20px;
            margin-top: 80px;
            justify-content: end;
            align-items: center;
            margin-right: 120px;
        }
        .order, .contact {
            height: 50px;
            background-color: cyan;
            width: 100px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1 {
            font-size: 15px;
            margin: 0;
            color:white;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Food Chart for Selected Hotel</h2>
        <div id="foodChart"></div>
    </div>

    <div class="container1">
        <button class="order btn btn-info text-white fw-bold">Order</button>
        <div class="contact"><h1>Contact</h1></div>
    </div>

    <div id="message" class="text-center fw-bold mt-3"></div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const hotelId = urlParams.get('hotel_id');

        if (hotelId) {
            fetch(`/AnnSeva/php_files/get_food_chart.php?hotel_id=${hotelId}`)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('foodChart');

                    if (data.length === 0) {
                        container.innerHTML = `<div class="alert alert-warning">No food chart available for this hotel.</div>`;
                        return;
                    }

                    let tableHTML = `
                        <table class="table table-bordered table-hover bg-white">
                            <thead class="table-dark">
                                <tr>
                                    <th>Food Item</th>
                                    <th>Quantity</th>
                                    <th>Time Left</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.forEach(item => {
                        tableHTML += `
                            <tr>
                                <td>${item.food_name}</td>
                                <td>${item.quantity}</td>
                                <td>${item.time}</td>
                            </tr>
                        `;
                    });

                    tableHTML += '</tbody></table>';
                    container.innerHTML = tableHTML;
                })
                .catch(error => {
                    console.error(error);
                    document.getElementById('foodChart').innerHTML = `<div class="alert alert-danger">Error loading food chart.</div>`;
                });
        } else {
            document.getElementById('foodChart').innerHTML = `<div class="alert alert-danger">No hotel selected.</div>`;
        }

        // Order button click
        document.querySelector('.order').addEventListener('click', () => {
            fetch('/AnnSeva/php_files/handle_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    hotel_id: hotelId,
                    ngo_username: "<?php echo $ngo_username; ?>",
                    contact: "<?php echo $ngo_contact; ?>"
                })
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById('message');
                if (data.success) {
                    msg.innerHTML = `<div class="alert alert-success">Order sent successfully!</div>`;
                } else {
                    msg.innerHTML = `<div class="alert alert-danger">Failed to send order</div>`;
                }
            })
            .catch(err => {
                document.getElementById('message').innerHTML = `<div class="alert alert-danger">Something went wrong.</div>`;
                console.error(err);
            });
        });
        // Fetch and display hotel contact
        if (hotelId) {
            fetch(`/AnnSeva/php_files/get_hotel_info.php?hotel_id=${hotelId}`)
                .then(res => res.json())
                .then(hotel => {
                    const contactDiv = document.querySelector('.contact');
                    contactDiv.innerHTML = `<h1><a href="tel:${hotel.phoneNumber}" style="text-decoration:none;color:black;">Call Hotel</a></h1>`;
                })
                .catch(err => {
                    console.error("Error fetching hotel contact:", err);
                });
        }

    </script>
</body>
</html>
