<?php
session_start();
include('connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$custId = $_SESSION['userid'];
$error = '';
$success = '';

// Check if there are messages in the session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Clear session message after using
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Clear session message after using
}

// Add new water consumption record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_water'])) {
    $drinkingTime = $_POST['drinkingTime'];
    $drinkingDate = $_POST['drinkingDate'];
    $waterIntake = $_POST['waterIntake'];

    if ($drinkingTime && $drinkingDate && $waterIntake) {
        $query = "INSERT INTO waterConsumption (custId, drinkingTime, drinkingDate, waterIntake) VALUES ('$custId', '$drinkingTime', '$drinkingDate', '$waterIntake')";
        if (mysqli_query($conn, $query)) {
            $success = "Water consumption record added successfully!";
        } else {
            $error = "Error adding water consumption: " . mysqli_error($conn);
        }
    } else {
        $error = "Please enter all fields!";
    }
}

// Update water consumption record
if (isset($_POST['update_water'])) {
    $id = $_POST['id'];
    $drinkingTime = $_POST['drinkingTime'];
    $drinkingDate = $_POST['drinkingDate'];
    $waterIntake = $_POST['waterIntake'];

    if ($drinkingTime && $drinkingDate && $waterIntake) {
        $query = "UPDATE waterConsumption SET drinkingTime='$drinkingTime', drinkingDate='$drinkingDate', waterIntake='$waterIntake' WHERE id='$id' AND custId='$custId'";
        if (mysqli_query($conn, $query)) {
            $success = "Water consumption record updated successfully!";
        } else {
            $error = "Error updating water consumption record: " . mysqli_error($conn);
        }
    } else {
        $error = "Please enter all fields!";
    }
}

// Delete water consumption record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM waterConsumption WHERE id = '$id' AND custId='$custId'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Record deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting the record!";
    }
    header("Location: waterConsumption.php");
    exit();
}

// Fetch water consumption records for the customer
$query = "SELECT * FROM waterConsumption WHERE custId = '$custId' ORDER BY drinkingDate ASC";
$result = mysqli_query($conn, $query);

// Search for water records
$searchType = '';
$searchValue = '';

if (isset($_POST['search'])) {
    $searchType = $_POST['searchType'];
    
    
    switch ($searchType) {
        case 'drinkingDate':
            $searchValue = $_POST['searchValue'];
            $query = "SELECT * FROM waterConsumption WHERE custId = '$custId' AND drinkingDate = '$searchValue'ORDER BY drinkingDate ASC";
            break;
        case 'last_week':
            $startOfLastWeek = date('Y-m-d', strtotime('last Sunday -1 week')); // Last week's Sunday
            $endOfLastWeek = date('Y-m-d', strtotime('last Saturday')); // Last week's Saturday
            $query = "SELECT * FROM waterConsumption WHERE custId = '$custId' AND drinkingDate BETWEEN '$startOfLastWeek' AND '$endOfLastWeek' ORDER BY drinkingDate ASC";
            break;
        case 'last_month':
            $startOfLastMonth = date('Y-m-01', strtotime('first day of last month')); // First day of last month
            $endOfLastMonth = date('Y-m-t', strtotime('last day of last month')); // Last day of last month
            $query = "SELECT * FROM waterConsumption WHERE custId = '$custId' AND drinkingDate BETWEEN '$startOfLastMonth' AND '$endOfLastMonth' ORDER BY drinkingDate ASC";
            break;
        default:
            $query = "SELECT * FROM waterConsumption WHERE custId = '$custId' ORDER BY recordDate ASC";
    }
}
$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Water Consumption Tracker</title>
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

        /* Style for the select dropdown */
        .modal select,
        form select {
            width: 100%;
            padding: 15px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            box-sizing: border-box;
            font-size: 14px;
            font-family: inherit;
            background-color: #fff;
            color: #333;
            appearance: none;
            /* Remove default arrow for a custom one */
            background-image: url("dropdown-icon.png");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
        }

        .action-button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 14px;
            margin: 0 5px;
        }

        .update-button {
            background-color: #4CAF50; 
            color: white;
        }

        .update-button:hover {
            background-color: #45a049;
        }

        .delete-button {
            background-color: #f44336; 
            color: white;
        }

        .delete-button:hover {
            background-color: #e53935;
        }

        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 100%; 
            max-width: 450px; 
            border-radius: 10px;
        }
        .no-result{
            width: 300px;
        }
        .modal .no-result{
            height: 150px;
        }
        .modal-content .no-result{
            height: 150px;
            width: 450px;
            text-align: center;
            padding-top: 30px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form card styling */
        .form-card {
            max-width: 650px;
            margin: 20px;
            margin-bottom: 50px;
            padding: 30px;
            background-color: #ddd5f3;
            border-radius: 25px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .add-button {
            background-image: url("gradient.png");
            color: #ffffff;
            border-radius: 15px;
            padding: 13px;
            margin-top: 20px;
        }
        .add-button:hover {
            background: linear-gradient(145deg, #4b0082, #301934);
        }
        .button-container {
            display: flex;
            gap: 10px;
        }

        .reset-button{
            height: 40px;
            width: 70px;
            justify-content: center;
        }

        .search-button {
            background-image: url("gradient.png");    
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            height: 40px;
        }
        .search-button:hover {
            background: linear-gradient(145deg, #4b0082, #301934);
        }

        .search-button img {
            width: 20px;
            height: 20px;
        }

        .blue-search-button {
            margin-top: 15px;
        }
        
        .modal-content input[type="submit"] {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
        }

        .modal-content input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .modal input[type="text"], .modal select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 20px;
        }

        #customWaterIntake {
        border: none; 
        border-bottom: 1px solid #000; 
        }

        #customWaterIntake:focus {
            outline: none; 
            border-bottom: 2px solid #333; 
        }

        #message-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .message {
            display: none;
            padding: 15px;
            margin: 5px;
            border-radius: 5px;
            color: white;
            opacity: 0;
            transition: opacity 0.5s;
            text-align: center;
            min-width: 200px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form input[type="text"], form input[type="number"], form input[type="date"], form input[type="time"] {
            width: calc(100% - 20px);
            padding: 13px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 15px;
        }

        form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 15px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        h1{
            padding: 20px;
        }

    </style>
</head>
<body>
    <div id="message-container"></div>
    <div class="container">
        <div class="navbar">
            <a href="customer_home.php">Home</a>
            <a href="bodyWeight.php">Body Weight</a>
            <a href="exerciseRoutine.php">Exercise Routine</a>
            <a href="waterConsumption.php" class="active">Water Consumption</a>
            <a href="bookingConsultant.php">Booking Consultant</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <h1>Water Consumption Tracker</h1>

        <div class="form-card">
        <form method="POST" action="" onsubmit="return validateForm()">
            <label for="drinkingDate">Date: </label>
            <input type="date" name="drinkingDate" id="drinkingDate" required><br>
            <label for="drinkingTime">Time: </label>
            <input type="time" name="drinkingTime" id="drinkingTime" required><br><br>
            
            <label for="waterIntake">Water Intake (mℓ):</label><br><br>
            <div>
                <label><input type="radio" name="waterIntake" value="500" required> 500</label>
                <label><input type="radio" name="waterIntake" value="1000"> 1000 </label>
                <label><input type="radio" name="waterIntake" value="1500"> 1500 </label>
                <label><input type="radio" name="waterIntake" value="2000"> 2000 </label>
                <label><input type="radio" name="waterIntake" value="2500"> 2500 </label>
                <label><input type="radio" name="waterIntake" value="2500"> 3000 </label>
                <label><input type="radio" name="waterIntake" value="custom" id="customRadio"> Custom:</label>
                <input type="number" step="0.01" name="customWaterIntake" id="customWaterIntake" placeholder="Enter amount"
                    style="display:none; border: none;  border-bottom: 1px solid #000; width: 100px;" required>
            </div>
            <br>
            <button type="submit" name="add_water" class="action-button add-button">Add Water Consumption</button>
        </form>
        </div>

        <div style="display:flex; justify-content: space-between; align-items: center;">
        <h2>Your Water Consumption Records</h2>
        <div class="button-container">
            <form method="POST" action="">
                <button type="submit" class="search-button reset-button" name="reset">Reset</button>
            </form>
            <button class="search-button" onclick="openSearchModal()">
                <img src="search-icon.png" alt="Search"> Search
            </button>
        </div>

        <div id="searchModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeSearchModal()">&times;</span>
                <h2>Search Water Consumption Records</h2>

                <form method="POST" action="">
                    <label for="searchType">Search by:</label>
                    <select name="searchType" id="searchType" required onchange="toggleSearchInput()">
                        <option value="select">Select...</option>
                        <option value="drinkingDate">Date</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                    </select>
                    <div id="searchInputContainer" style="display:none;">
                        <input type="text" name="searchValue" placeholder="Enter value" value="<?php echo htmlspecialchars($searchValue); ?>" required>
                    </div>
                    <input type="submit" class="blue-search-button" name="search" value="Search">
                </form>
            </div>
        </div>
        </div>
        <table>
            <tr>
                <th>Drinking Date</th>
                <th>Drinking Time</th>
                <th>Water Intake (mℓ)</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['drinkingDate']; ?></td>
                    <td><?php echo $row['drinkingTime']; ?></td>
                    <td><?php echo $row['waterIntake']; ?></td>
                    <td>
                        <button class="action-button update-button" onclick="openUpdateModal('<?php echo $row['id']; ?>',
                        '<?php echo $row['drinkingDate']; ?>', '<?php echo $row['drinkingTime']; ?>', '<?php echo $row['waterIntake']; ?>')">Update</button>
                        <a href="waterConsumption.php?delete=<?php echo $row['id']; ?>" class="action-button delete-button" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h2>Update Water Consumption Record</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="updateId">
                <input type="date" name="drinkingDate" id="updateDrinkingDate" required>
                <input type="time" name="drinkingTime" id="updateDrinkingTime" required>
                <input type="number" name="waterIntake" id="updateWaterIntake" required placeholder="Water Intake (ml)">
                <button type="submit" name="update_water" class="action-button update-button">Update</button>
                <button type="button" class="action-button delete-button" onclick="closeUpdateModal()">Cancel</button>
            </form>
        </div>
    </div>
    <div id="noRecordModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="no-result">
                <span class="close" onclick="closeNoRecordModal()">&times;</span>
                <h2>No Record Found</h2>
                <p>There are no records matching your search criteria.</p>
            </div>
        </div>
    </div>

    <script>
        function setDefaultDateTime() {
            const options = { timeZone: 'Asia/Kuala_Lumpur', hour12: false, hour: '2-digit', minute: '2-digit' };
        
            // Get Malaysia's current date
            const date = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Kuala_Lumpur' }).format(new Date());
            document.getElementById('drinkingDate').value = date;

            // Get Malaysia's current time
            const time = new Intl.DateTimeFormat('en-GB', options).format(new Date());
            document.getElementById('drinkingTime').value = time;
        }

        // Show a line if "Custom" is selected
        document.querySelectorAll('input[name="waterIntake"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const customInput = document.getElementById('customWaterIntake');
                customInput.style.display = this.value === 'custom' ? 'inline-block' : 'none';
                if (this.value !== 'custom') {
                    customInput.value = ''; // Clear the custom input if not selected
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const customRadio = document.getElementById('customRadio');
            const customWaterIntake = document.getElementById('customWaterIntake');

            customRadio.addEventListener('change', function() {
                customWaterIntake.style.display = 'inline-block';
                customWaterIntake.required = true;
            });

            document.querySelectorAll('input[name="waterIntake"]').forEach(radio => {
                if (radio.id !== 'customRadio') {
                    radio.addEventListener('change', function() {
                        customWaterIntake.style.display = 'none';
                        customWaterIntake.required = false;
                        customWaterIntake.value = '';
                    });
                }
            });
        });

        function validateForm() {
            const customInput = document.getElementById('customWaterIntake');
            const customRadio = document.querySelector('input[name="waterIntake"][value="custom"]');
        
            if (customRadio.checked && !customInput.value) {
                alert("Please enter a value for the custom water intake.");
                return false; 
            }

            // Replace the "custom" word with input value
            if (customRadio.checked) {
                const customValue = customInput.value;
                const hiddenRadio = document.querySelector('input[name="waterIntake"][value="custom"]');
                hiddenRadio.value = customValue; // Set the value to the actual custom input
            }
            return true;
        }

        window.onload = setDefaultDateTime;

        function toggleSearchInput() {
            const searchType = document.getElementById('searchType').value;
            const searchInputContainer = document.getElementById('searchInputContainer');

            if (searchType === 'drinkingDate') {
                searchInputContainer.style.display = 'block'; 
                searchInputContainer.innerHTML = '<input type="date" name="searchValue" required>';
            } else if (searchType === 'last_week' || searchType === 'last_month') {
                searchInputContainer.style.display = 'none'; 
                searchInputContainer.innerHTML = ''; 
            } else {
                searchInputContainer.style.display = 'block'; 
                searchInputContainer.innerHTML = '<input type="text" name="searchValue" placeholder="Enter value" value="<?php echo htmlspecialchars($searchValue); ?>" required>';
            }
        }

        function openUpdateModal(id, drinkingDate, drinkingTime, waterIntake) {
            document.getElementById('updateId').value = id;
            document.getElementById('updateDrinkingDate').value = drinkingDate;
            document.getElementById('updateDrinkingTime').value = drinkingTime;
            document.getElementById('updateWaterIntake').value = waterIntake;
            document.getElementById('updateModal').style.display = 'block';
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        function openSearchModal() {
            document.getElementById('searchModal').style.display = 'block';
        }

        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }

        // Close modal if the user clicks outside of it
        window.onclick = function(event) {
            const updateModal = document.getElementById('updateModal');
            const searchModal = document.getElementById('searchModal');
            if (event.target === updateModal) {
                closeUpdateModal();
            } else if (event.target === searchModal) {
                closeSearchModal();
            }
        }

        function openNoRecordModal() {
            document.getElementById('noRecordModal').style.display = 'block';
            document.getElementById('noRecordModal').style.zIndex = '100'; 
        }

        function closeNoRecordModal() {
            document.getElementById('noRecordModal').style.display = 'none';
            window.location.href = "waterConsumption.php";//Show all records
        }
        <?php if (mysqli_num_rows($result) == 0 && isset($_POST['search'])) { ?>
            openNoRecordModal(); // Open modal if no records found
        <?php } ?>

        // Show message
        function showMessage(type, message) {
            const messageContainer = document.createElement('div');
            messageContainer.className = `message ${type}`;
            messageContainer.textContent = message;

            document.getElementById('message-container').appendChild(messageContainer);

            // Show the message
            messageContainer.style.display = 'block';
            setTimeout(() => {
                messageContainer.style.opacity = 1; // Fade in
            }, 10);

            // Hide after 3 seconds
            setTimeout(() => {
                messageContainer.style.opacity = 0; // Fade out
                setTimeout(() => {
                    messageContainer.remove(); // Remove from DOM after fade out
                }, 500); // Wait for fade out to finish
            }, 3000); // Show for 3 seconds
        }
        <?php if ($error) { ?>
            showMessage('error', '<?php echo addslashes($error); ?>');
        <?php } ?>
        <?php if ($success) { ?>
            showMessage('success', '<?php echo addslashes($success); ?>');
        <?php } ?>

    </script>
</body>
</html>
