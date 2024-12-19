<?php
// Start the session
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="general.css"> 
    <title>Payment Success</title>
    <style>
        .navbar {
            display: flex;
            justify-content: center; 
            background: linear-gradient(145deg, #f3e1f1, #d1c2e2);
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; 
        }

        .navbar a {
            text-decoration: none;
            padding: 10px 20px;
            color: #555;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .navbar a:hover {
            background: rgba(200, 200, 255, 0.1);
            color: #6d5acf;
        }

        .navbar .active {
            background: rgba(200, 150, 255, 0.2);
            color: #7a5dd6;
        }

        .navbar .active:hover {
            background: rgba(200, 150, 255, 0.3);
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }


        .notification {
            background-color: #d4edda;
            color: #155724;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Smaller width */
            text-align: center;
            margin: 0 auto; /* Centering the notification */
        }

        .notification h2 {
            margin-top: 10px;
            margin-bottom: 0;
            font-size: 20px; /* Slightly smaller font size */
        }

        .notification p {
            font-size: 14px; /* Slightly smaller font size */
            margin: 10px 0;
        }

        .redirect-button {
            margin-top: 22px;
            padding: 10px 20px;
            background-color: rgb(43, 182, 115);
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-bottom: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);

        }

        .redirect-button:hover {
            background-color: #218838;
        }

        .checkmark img {
            width: 80px; /* Adjust the size of the checkmark */
            height: 80px; /* Adjust the size of the checkmark */
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="customer_home.php" class="active">Home</a>
            <a href="bodyWeight.php">Body Weight</a>
            <a href="exerciseRoutine.php">Exercise Routine</a>
            <a href="waterConsumption.php">Water Consumption</a>
            <a href="bookingConsultant.php" >Booking Consultant</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="notification">
            <div class="checkmark">
                <img src="https://cdn2.iconfinder.com/data/icons/greenline/512/check-512.png">
            </div>
            <h2>Payment Successful!</h2>
            <p>Your payment has been processed successfully.</p>
            <form action="customer_home.php" method="get">
                <button type="submit" class="redirect-button">Back to Home</button>
            </form>
        </div>
    </div>
</body>
</html>
