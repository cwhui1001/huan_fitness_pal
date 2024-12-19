<?php
session_start();
include('connection.php');

$success = '';
$error = '';

// Check if there are messages in the session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Clear session message after using
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Clear session message after using
}


// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_booking'])) {
    $custID = $_POST['custID'];
    $nutritionistName = $_POST['nutritionistName'];
    $predate = $_POST['predate'];
    $pretime = $_POST['pretime'];
    $remarks = $_POST['remarks'];

    if ($custID && $nutritionistName && $predate && $pretime) {
        // Change the table name to userrequest
        $query = "INSERT INTO userrequest (custID, nutritionistName, preDate, preTime, remarks) 
                  VALUES ('$custID', '$nutritionistName', '$predate', '$pretime', '$remarks')";

        if (mysqli_query($conn, $query)) {
            $success = "Booking added successfully!";
        } else {
            $error = "Error adding booking: " . mysqli_error($conn);
        }
    } else {
        $error = "Please enter all field!";
    }
}

// Update booking request
if (isset($_POST['update_booking_request'])) {
    $requestId = $_POST['requestId'];
    $nutritionistName = $_POST['nutritionistName'];
    $predate = $_POST['predate'];
    $pretime = $_POST['pretime'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];
    $reason = $_POST['reason'] ?? '';

    if ($predate && $pretime && $nutritionistName && $remarks && $status) {
        $query = "UPDATE userrequest 
          SET predate='$predate', pretime='$pretime', nutritionistName='$nutritionistName', remarks='$remarks', status='$status', reason='$reason' 
          WHERE requestId='$requestId'";
        if (mysqli_query($conn, $query)) {
            $success = "Booking updated successfully!";
        } else {
            $error = "Error updating booking: " . mysqli_error($conn);
        }
    } else {
        $error = "Please enter all fields!";
    }
}


// Delete weight record 
if (isset($_GET['delete'])) {
    $requestId = $_GET['delete'];
    $query = "DELETE FROM userrequest WHERE requestId = '$requestId'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Record deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting the record!" . mysqli_error($conn);
    }
    header("Location: userRequest.php");
    exit;
}

// Search for weight records
$searchType = '';
$searchValue = '';

if (isset($_POST['search'])) {
    $searchType = $_POST['searchType'];
    $searchValue = $_POST['searchValue'];

    // Ensure valid query generation based on search type
    switch ($searchType) {
        case 'custID':
            $query = "SELECT * FROM userrequest WHERE custID = '$searchValue'";
            break;
        case 'nutritionistName':
            $query = "SELECT * FROM userrequest WHERE nutritionistName = '$searchValue'";
            break;
        case 'predate':
            $query = "SELECT * FROM userrequest WHERE predate = '$searchValue'";
            break;
        case 'pretime':
            $query = "SELECT * FROM userrequest WHERE pretime = '$searchValue'";
            break;
        default:
            $query = "SELECT * FROM userrequest";
    }
    $searchType = '';
    $searchValue = '';
} else {
    $query = "SELECT * FROM userrequest";
}

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>User Request</title>
    <link rel="stylesheet" type="text/css" href="general.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            overflow-x: auto;
        }

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

        .action-button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            /* transition:  */
            background: 0.3s ease;
            font-size: 14px;
            margin: 0 5px;
        }

        .update-button {
            background-color: #4CAF50;
            color: white;
            margin-bottom: 15px;
        }

        .update-button:hover {
            background-color: #45a049;
        }

        .cancel-button {
            background-color: #e2e6ea;
            color: #333;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .cancel-button:hover {
            background-color: #cbd3d9;
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
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            transform: translateY(-10%);
            position: relative;
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

        .search-button {
            background-image: url("gradient.png");
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .search-button img {
            width: 20px;
            height: 20px;
        }

        .add-button {
            background-image: url("gradient.png");
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            width: 80px;
        }

        .add-button img {
            width: 20px;
            height: 20px;
        }

        .modal input[type="text"],
        .modal select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        form {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        form input,
        form select,
        form input[type="date"],
        form input[type="time"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            resize: none;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        .table {
            background-color: white;

        }
    </style>
</head>

<body>
    <div id="message-container"></div>
    <div class="container">
        <div class="navbar">
            <a href="admin_home.php">Home</a>
            <a href="userRequest.php" class="active">User Request</a>
            <a href="classMember.php">Class Member</a>
            <a href="logout.php">Logout</a>
        </div>

        <h1>User's Consultant Booking Request</h1>

        <div style="display:flex; justify-content: space-between; align-items: center;">

            <button class="add-button" onclick="openAddModal()">
                <img src="https://cdn-icons-png.flaticon.com/512/6335/6335606.png" alt="Request Consultant"> Add
            </button>

            <button class="search-button" onclick="openSearchModal()">
                <img src="search-icon.png" alt="Search"> Search
            </button>

            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddModal()">&times;</span>
                    <h2>Add Booking Request</h2>

                    <form method="POST" action="">
                        <label for="custID">Enter User ID:</label>
                        <input type="text" name="custID" placeholder="User ID" required><br>
                        <label for="nutritionistName">Select Nutritionist:</label>
                        <select name="nutritionistName" required>
                            <option value="">Choose a Nutritionist</option>
                            <option value="John Martinez">Dr. John Martinez</option>
                            <option value="Emily Wong">Dr. Emily Wong</option>
                            <option value="Nurul Amira">Dr. Nurul Amiral</option>
                            <option value="Jessica Chen">Dr. Jessica Chen</option>
                        </select>

                        <label for="predate">Preferred Date:</label>
                        <input type="date" name="predate" max="2024-12-31" min="<?php echo date("Y-m-d")?>"required>

                        <label for="pretime">Preferred Time:</label>
                        <input type="time" name="pretime" required>

                        <label for="remarks">Remarks (Optional):</label>
                        <textarea name="remarks" rows="4"
                            placeholder="Any additional information or comments"></textarea>

                        <input type="submit" name="add_booking" value="Request Consultant">
                    </form>
                </div>
            </div>
        </div>

        <div id="searchModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeSearchModal()">&times;</span>
                <h2>Search Booking Request</h2>

                <form method="POST" action="">
    <label for="searchType">Search by:</label>
    <select name="searchType" id="searchType" required onchange="updateInputField()">
        <option value="">Select...</option>
        <option value="custID">Customer ID</option>
        <option value="nutritionistName">Nutritionist Name</option>
        <option value="predate">Preferred Date</option>
        <option value="pretime">Preferred Time</option>
    </select>

    <input type="text" id="searchValue" placeholder="Enter Customer ID" style="display: none;">
    <select id="searchDropdown" style="display: none;">
        <option value="">Select Nutritionist Name...</option>
        <option value="John Martinez">Dr. John Martinez</option>
        <option value="Emily Wong">Dr. Emily Wong</option>
        <option value="Nurul Amira">Dr. Nurul Amira</option>
        <option value="Jessica Chen">Dr. Jessica Chen</option>
    </select>
    <input type="date" id="predate" style="display: none;">
    <input type="time" id="pretime" style="display: none;">

    <input type="submit" name="search" value="Search">
</form>

            </div>
        </div>

        <table>
            <tr>
                <th>Request ID</th>
                <th>Customer ID</th>
                <th>Request Date</th>
                <th>Preferred Date</th>
                <th>Preferred Time</th>
                <th>Nutritionist Name</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>Reject Reason</th>
                <th>Action</th>
            </tr>
            <?php
            date_default_timezone_set('Asia/Kuala_Lumpur');
            // Get today's date and current time
            $today = date('Y-m-d');
            $currentTime = date('H:i:s');  // Get current time in the format HH:MM:SS
            
            while ($row = mysqli_fetch_assoc($result)) {
                $isPastDate = false;

                // Check if the preferred date is before today or if it's today but the time has passed
                if ($row['predate'] == $today && $row['pretime'] < $currentTime) {
                    // If the date is before today, it's past
                    $isPastDate = true;
                } elseif ($row['predate'] < $today) {
                    // If the date is today, check if the time is in the past
                    $isPastDate = true;
                }
                ?>
                <tr style="background-color: <?php echo $isPastDate ? '#fee2e2' : 'transparent'; ?>;">
                    <td><?php echo htmlspecialchars($row['requestId']); ?></td>
                    <td><?php echo htmlspecialchars($row['custID']); ?></td>
                    <td><?php echo htmlspecialchars($row['requestdate']); ?></td>
                    <td><?php echo htmlspecialchars($row['predate']); ?></td>
                    <td><?php echo htmlspecialchars($row['pretime']); ?></td>
                    <td><?php echo htmlspecialchars($row['nutritionistName']); ?></td>
                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                    <td>
                        <button class="action-button update-button" onclick="openUpdateModal('<?php echo addslashes(htmlspecialchars($row['requestId'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['predate'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['pretime'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['nutritionistName'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['remarks'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['status'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['reason'] ?? '')); ?>')">Update
                        </button>
                        <a href="userrequest.php?delete=<?php echo $row['requestId']; ?>"
                            class="action-button delete-button"
                            onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>

            <?php } ?>
        </table>
    </div>

    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h2>Update Booking Request</h2>
            <form method="POST" action="">
                <input type="hidden" name="requestId" id="updaterequestid">
                <label for="predate">Preferred Date:</label>
                <input type="date" name="predate" max="2024-12-31" min="<?php echo date("Y-m-d")?>" id="updatepredate" required><br>
                <label for="pretime">Preferred Time:</label>
                <input type="time" name="pretime" id="updatepretime" required><br>
                <label for="nutritionistName">Nutritionist Name:</label>
                <select name="nutritionistName" id="updatenutritionistname" required>
                    <option value="">Choose a Nutritionist</option>
                    <option value="John Martinez">Dr.John Martinez</option>
                    <option value="Emily Wong">Dr.Emily Wong</option>
                    <option value="Nurul Amira">Dr.Nurul Amira</option>
                    <option value="Jessica Chen">Dr.Jessica Chen</option>
                </select><br>

                <label for="remarks">Remarks:</label>
                <input type="text" name="remarks" id="updateremark" required><br>

                <label for="status">Booking Status:</label>
                <select name="status" id="updatestatus" required onchange="checkStatus()">
                    <option value="">Booking Status</option>
                    <option value='"Pending"'>Pending</option>
                    <option value='"Reject"'>Reject</option>
                    <option value='"Success"'>Success</option>
                </select><br>

                <label for="reason">Reject Reason:</label>
                <input type="text" name="reason" id="rejectreason" disabled><br>
                <input type="submit" name="update_booking_request" value="Update Booking">
                <button type="button" class="cancel-button" onclick="closeUpdateModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(requestId, predate, pretime, nutritionistName, remarks, status, reason) {
            document.getElementById('updaterequestid').value = requestId;
            document.getElementById('updatepredate').value = predate;
            document.getElementById('updatepretime').value = pretime;
            document.getElementById('updatenutritionistname').value = nutritionistName;
            document.getElementById('updateremark').value = remarks;
            document.getElementById('updatestatus').value = status;
            document.getElementById('rejectreason').value = reason;
            checkStatus(); // Consolidate the reason handling here
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

        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }


        function updateInputField() {
            var searchType = document.getElementById("searchType").value;
            var searchValue = document.getElementById("searchValue");
            var searchDropdown = document.getElementById("searchDropdown");
            var predateInput = document.getElementById("predate");
            var pretimeInput = document.getElementById("pretime");

            // Hide all inputs and remove 'searchValue' name attribute by default
            searchValue.style.display = "none";
            searchDropdown.style.display = "none";
            predateInput.style.display = "none";
            pretimeInput.style.display = "none";

            searchValue.removeAttribute("name");
            searchDropdown.removeAttribute("name");
            predateInput.removeAttribute("name");
            pretimeInput.removeAttribute("name");

            // Show the appropriate input based on the selected search type and set 'searchValue' name
            if (searchType === "nutritionistName") {
                searchDropdown.style.display = "block";
                searchDropdown.setAttribute("name", "searchValue");
            } else if (searchType === "predate") {
                predateInput.style.display = "block";
                predateInput.setAttribute("name", "searchValue");
            } else if (searchType === "pretime") {
                pretimeInput.style.display = "block";
                pretimeInput.setAttribute("name", "searchValue");
            } else {
                searchValue.style.display = "block";
                searchValue.setAttribute("name", "searchValue");
            }
        }

        function clearSearchFields() {
            document.getElementById('searchType').selectedIndex = 0;
            document.getElementById('searchValue').value = '';
            document.getElementById('searchDropdown').selectedIndex = 0;
            document.getElementById('predate').value = '';
            document.getElementById('pretime').value = '';
        }

        // Pop-up message
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

        window.onload = function () {
            <?php if ($success) { ?>
                showMessage('success', '<?php echo addslashes($success); ?>');
            <?php } elseif ($error) { ?>
                showMessage('error', '<?php echo addslashes($error); ?>');
            <?php } ?>
        };

        function checkStatus() {
            var statusSelect = document.getElementById("updatestatus");
            var reasonInput = document.getElementById("rejectreason");

            // Enable or disable the reason input based on the selected status
            if (statusSelect.value === '"Reject"') {
                reasonInput.disabled = false; // Enable input if "Rejected" is selected
                reasonInput.required = true;  // Make it required
            } else {
                reasonInput.value = "";        // Clear the input value if not rejected
                reasonInput.disabled = true;   // Disable the input
                reasonInput.required = false;  // Not required if other options are selected
            }
        }
    </script>
</body>

</html>