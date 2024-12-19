<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$cust_id = $_SESSION['userid'];
$requestDate = date('Y-m-d H:i:s');

if (isset($_SESSION['nutritionistName']) && isset($_SESSION['preferredDate']) 
    && isset($_SESSION['preferredTime']) && isset($_SESSION['remarks'])){
    $nutritionistName = $_SESSION['nutritionistName'];
    $preferredDate = $_SESSION['preferredDate'];
    $preferredTime = $_SESSION['preferredTime'];
    $remarks = $_SESSION['remarks'];
}

unset($_SESSION['booking']);

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {

    $query = "INSERT INTO userrequest (custID, requestdate, predate, pretime, nutritionistName, remarks) 
                VALUES (?, ?, ?, ?, ?, ?)";

    // Initialize the prepared statement
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        die("Error preparing the statement: " . mysqli_error($conn));
    }

    // Bind the parameters to the placeholders
    if (!mysqli_stmt_bind_param($stmt, 'ssssss', $cust_id, $requestDate, $preferredDate, $preferredTime, $nutritionistName, $remarks)) {
        die("Error binding parameters: " . mysqli_stmt_error($stmt));
    }

    // Execute the prepared statement
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error executing query: " . mysqli_stmt_error($stmt);
    } else {
        header("Location: payment_success.php");
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for Booking</title>
    <link rel="stylesheet" href="general.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

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

        .main-container {
            display: flex;
            margin: 0 20px;
        }

        .description {
            width: 40%;
            padding: 20px;
            background-color: #f4f4f9;
            border-radius: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .payment-form-container {
            width: 60%;
            padding: 25px;
            margin-left: 20px;
            background-color: #ddd5f3;
            border-radius: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .payment-form input, .payment-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .button-group {
            display: flex;
            justify-content:space-between;
            margin-top: 10px;
            padding: 10px;
        }

        .button-group input[type="submit"].submit {
            background-image: url("gradient.png");
            transition: background-color 0.3s;
            height: 40px;
            width: 100%;
            margin-left: 10px;
            font-size: 15px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 15px;
        }

        .button-group input[type="button"].back-link {
            background-image: url("gradient.png");
            text-decoration: none;
            width: 20%;
            font-size: 15px;
            color: white; 
            border: none;
            cursor: pointer;
            border-radius: 15px;
        }
        .back-link:hover {
            background: linear-gradient(145deg, #4b0082, #301934) !important;
        }
        .submit:hover {
            background: linear-gradient(260deg, #301934, #4b0082) !important;

        }
        form input[type="number"], form input[type="text"],
        select {
            width: 100%;
            padding: 13px;
            margin-bottom: 15px; 
            border: 1px solid #ccc;
            border-radius: 15px;
            transition: border-color 0.3s;
            box-sizing: border-box; 
        }
        
        form input[type="number"]:focus, form input[type="text"]:focus,
        select:focus {
            border-color: #4c45e0;
            outline: none;
            width: 100%;
        }
        .success {
            color: green;
        }

        .error {
            color: red;
        }
        h2{
            margin-bottom: 25px;
        }
        .info-table td {
            padding: 6px 0;
            text-align: left;
            border: none;
        }
        .info-table td:first-child {
            width: 150px;
        }
    </style>
    <script>
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            
            let formattedValue = '';
            for (let i = 0; i < value.length; i += 4) {
                if (i > 0) {
                    formattedValue += ' ';
                }
                formattedValue += value.substring(i, i + 4);
            }

            input.value = formattedValue;
        }

        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 4) {
                value = value.slice(0, 4);
            }

            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }

            input.value = value;
        }

        function validateExpiryDate() {
            const expiryInput = document.getElementById('expiry-date').value;
            const monthYear = expiryInput.split('/');

            if (monthYear.length !== 2) {
                alert('Please enter a valid expiry date in MM/YY format.');
                return false;
            }

            const month = parseInt(monthYear[0], 10);
            const year = parseInt(monthYear[1], 10);

            if (month < 1 || month > 12) {
                alert('Please enter a valid month (01-12).');
                return false;
            }

            let currentDate = new Date();
            const currentYear = new Date().getFullYear() % 100;
            if (year < currentYear) {
                alert('The expiry year cannot be in the past.');
                return false;
            }

            const currentMonth = currentDate.getMonth() + 1;
            if(year == currentYear && month < currentMonth) {
                alert('The expiry month cannot be in the past.');
                return false;
            }

            return true; 
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="customer_home.php">Home</a>
            <a href="bodyWeight.php">Body Weight</a>
            <a href="exerciseRoutine.php">Exercise Routine</a>
            <a href="waterConsumption.php">Water Consumption</a>
            <a href="bookingConsultant.php" class="active">Booking Consultant</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="main-container">
            <div class="description">
                <h2>Payment Details</h2>
                <p>
                    Please complete your payment to confirm the booking with your consultant. Ensure that the card details you provide are valid. 
                </p>
                <table class="info-table">
                    <tr>
                        <td>&bull; <strong>Nutritionist Name</strong></td>
                        <td>:&nbsp;</td>
                        <td><?php echo $nutritionistName; ?></td>
                    </tr>
                    <tr>
                        <td>&bull; <strong>Preferred Date</strong></td>
                        <td>:&nbsp;</td>
                        <td><?php echo $preferredDate; ?></td>
                    </tr>
                    <tr>
                        <td>&bull; <strong>Preferred Time</strong></td>
                        <td>:&nbsp;</td>
                        <td><?php echo $preferredTime; ?></td>
                    </tr>
                    <tr>
                        <td>&bull; <strong>Consultation Fee</strong></td>
                        <td>:&nbsp;</td>
                        <td>RM 20</td>
                    </tr>
                </table>
                <p>If you have any issues, please contact support at huanfitnesspal@gmail.com.</p>
            </div>

            <div class="payment-form-container">
                <h2>Credit Card</h2>
                <form method="POST" action="" onsubmit="return validateExpiryDate();">
                    <label for="cardholder-name">Cardholder's Name:</label>
                    <input type="text" id="cardholder-name" name="cardholder-name" placeholder="John Doe" required>

                    <label for="card-number">Card Number:</label>
                    <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" maxlength="19" required pattern="\d{4} \d{4} \d{4} \d{4}" oninput="formatCardNumber(this)" required>

                    <label for="expiry-date">Expiry Date (MM/YY):</label>
                    <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/YY" maxlength="5" required oninput="formatExpiryDate(this)" required>

                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required pattern="\d{3}" required>

                    <label for="amount">Amount:</label>
                    <input type="text" id="amount" name="amount" value="RM 20" readonly>

                    <div class="button-group">
                        <input type="button" name="back" class="back-link" value="Back" onclick="history.back()">
                        <input type="submit" name="submit_payment" class="submit" value="Submit Payment">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
