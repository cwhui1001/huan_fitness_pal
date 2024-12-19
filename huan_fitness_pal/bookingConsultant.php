<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$custId = $_SESSION['userid'];
$success = '';
$error = '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_consultant'])) {
    $_SESSION['nutritionistName'] = $_POST['nutritionistName'];
    $_SESSION['preferredDate'] = $_POST['preferredDate'];
    $_SESSION['preferredTime'] = $_POST['preferredTime'];
    $_SESSION['remarks'] = $_POST['remarks'];

    $checkQuery = "SELECT * FROM userrequest 
    WHERE predate = ? 
    AND pretime = ? 
    AND nutritionistName = ?";

    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("sss", $_SESSION['preferredDate'], $_SESSION['preferredTime'], $_SESSION['nutritionistName']);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "The selected date and time is already booked. Please choose a different time.";
    } else {
        header('Location: payment.php'); // Redirect to payment page
        exit;
    }
}

$currentDate = date('Y-m-d');

// Fetch previous bookings for the logged-in user
$bookingsQuery = "SELECT predate, pretime, nutritionistName, remarks, requestDate, status, reason
                  FROM userrequest WHERE custId = '$custId' ORDER BY predate, pretime ASC";
$bookingsResult = mysqli_query($conn, $bookingsQuery);

$upcomingBookings = [];
$pastBookings = [];

// Iterate through the fetched bookings
while ($row = mysqli_fetch_assoc($bookingsResult)) {
    // Check if the booking is in the past
    if (strtotime($row['predate']) < strtotime($currentDate)) {
        $pastBookings[] = $row;  // Store past bookings
    } else {
        $upcomingBookings[] = $row;  // Store upcoming bookings
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Booking Consultant</title>
    <link rel="stylesheet" type="text/css" href="general.css">
    <style>
        .navbar {
            display: flex;
            justify-content: center; 
            background: linear-gradient(145deg, #f3e1f1, #d1c2e2);
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

        form {
            width: 650px;
            margin: 20px;
            background-color: #ddd5f3;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        h1{
            padding: 20px;
        }
        form input, form input[type="date"], form select, form textarea {
            width: 100%;
            padding: 13px;
            margin: 15px 0;
            border-radius: 15px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            resize: none;
        }
        /* Style for the select dropdown */
        .modal select,
        form select {
            color: #333;
            appearance: none;
            /* Remove default arrow for a custom one */
            background-image: url("dropdown-icon.png");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
        }

        form input[type="submit"] {
            background-image: url("gradient.png");
            color: white;
            border: none;
            cursor: pointer;
            width: 170px;
            padding: 13px;
            border-radius: 15px;
            margin: 5px;
        }

        form input[type="submit"]:hover {
            background: linear-gradient(145deg, #4b0082, #301934);
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .past-booking {
            background-color: #ccc;
            color: #555;
        }
        
        
    </style>
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
        
        <h1>Booking Consultant</h1>

        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <label for="nutritionistName">Select Nutritionist:</label>
            <select name="nutritionistName" required>
                <option value="">Choose a Nutritionist</option>
                <option value="John Martinez">Dr. John Martinez</option>
                <option value="Emily Wong">Dr. Emily Wong</option>
                <option value="Nurul Amiral">Dr. Nurul Amiral</option>
                <option value="Jessica Chen">Dr. Jessica Chen</option>
            </select>

            <label for="preferredDate">Preferred Date:</label>
            <input type="date" name="preferredDate" min="<?php echo date('Y-m-d', strtotime('+2 day')); ?>" max="2025-12-31" required>

            <label for="preferredTime">Preferred Time:</label>
            <select name="preferredTime" required>
                <option value="">Choose a Time Slot</option>
                <option value="08:30-09:30">08:30 AM - 09:30 AM</option>
                <option value="09:30-10:30">09:30 AM - 10:30 AM</option>
                <option value="10:30-11:30">10:30 AM - 11:30 AM</option>
                <option value="11:30-12:30">11:30 AM - 12:30 PM</option>
                <option value="13:00-14:00">01:00 PM - 02:00 PM</option>
                <option value="14:00-15:00">02:00 PM - 03:00 PM</option>
                <option value="15:00-16:00">03:00 PM - 04:00 PM</option>
                <option value="16:00-17:00">04:00 PM - 05:00 PM</option>
            </select>

            <label for="remarks">Remarks (Optional):</label>
            <textarea name="remarks" rows="4" placeholder="Any additional information or comments"></textarea>
            <input type="submit" name="request_consultant" value="Request Consultant">
        </form>
        <br>
        <h2>All Bookings</h2>

        <?php if (mysqli_num_rows($bookingsResult) > 0): ?>
            <table>
                <tr>
                    <th>PreDate</th>
                    <th>PreTime</th>
                    <th>Nutritionist Name</th>
                    <th>Remarks</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Reject Reason</th>
                </tr>
                <?php foreach ($upcomingBookings as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['predate']); ?></td>
                    <td><?php echo htmlspecialchars($row['pretime']); ?></td>
                    <td><?php echo htmlspecialchars($row['nutritionistName']); ?></td>
                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                    <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['reason'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php foreach ($pastBookings as $row): ?>
                <tr class="past-booking">
                    <td><?php echo htmlspecialchars($row['predate']); ?></td>
                    <td><?php echo htmlspecialchars($row['pretime']); ?></td>
                    <td><?php echo htmlspecialchars($row['nutritionistName']); ?></td>
                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                    <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['reason'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No previous bookings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>