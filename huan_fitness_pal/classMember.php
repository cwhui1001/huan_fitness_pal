<?php
session_start();
include('connection.php');

if (!isset($_SESSION['userid'])) {
    header('Location: login.php');  // Redirect to login page if not logged in
    exit;
}
$success = '';
$error = '';

// Check if there are messages in the session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); 
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); 
}


// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    $custID = $_POST['custID'];
    $paymentDuration = $_POST['paymentDuration'];
    $fitnessType = $_POST['fitnessType'];
    $joinDate = date('Y-m-d');

    if ($custID && $paymentDuration && $fitnessType) {
        $expiryDate = date('Y-m-d', strtotime("+$paymentDuration months", strtotime($joinDate)));
        // Change the table name to fitnessclassmember
        $query = "INSERT INTO fitnessclassmember (custID, paymentDuration, fitnessType, joinDate, membershipExpiryDate) 
                  VALUES ('$custID', '$paymentDuration', '$fitnessType', '$joinDate', '$expiryDate')";

        if (mysqli_query($conn, $query)) {
            $success = "Member added successfully!";
        } else {
            $error = "Error adding member: " . mysqli_error($conn);
        }
    } else {
        $error = "Please enter all field!";
    }
}

// Update booking request
if (isset($_POST['update_member'])) {
    $memberID = $_POST['memberID'];
    $paymentDuration = (int)$_POST['paymentDuration'];
    $fitnessType = $_POST['fitnessType'];

    if ($fitnessType && $paymentDuration) {
        // Retrieve current joinDate
        $joinDateQuery = "SELECT joinDate FROM fitnessclassmember WHERE memberID = '$memberID'";
        $joinDateResult = mysqli_query($conn, $joinDateQuery);
        $joinDateRow = mysqli_fetch_assoc($joinDateResult);
        $joinDate = $joinDateRow['joinDate'];

        // Calculate new membership expiry date
        $expiryDate = date('Y-m-d', strtotime("+$paymentDuration months", strtotime($joinDate)));

        $query = "UPDATE fitnessclassmember 
                  SET fitnessType='$fitnessType', paymentDuration='$paymentDuration', membershipExpiryDate='$expiryDate'
                  WHERE memberID='$memberID'";

        if (mysqli_query($conn, $query)) {
            $success = "Member updated successfully!";
        } else {
            $error = "Error updating member: " . mysqli_error($conn);
        }
    } else {
        $error = "Please enter all fields!";
    }
}

// Delete  record 
if (isset($_GET['delete'])) {
    $memberID = $_GET['delete'];
    $query = "DELETE FROM fitnessclassmember WHERE memberID = '$memberID'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Record deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting the record!" . mysqli_error($conn);
    }
    header("Location: classMember.php");
    exit;
}

// Search for weight records
$searchType = '';
$searchValue = '';
if (isset($_POST['search'])) {
    $searchType = $_POST['searchType'];
    $searchValue = $_POST['searchValue'];

    switch ($searchType) {
        case 'custID':
            $query = "SELECT * FROM fitnessclassmember WHERE  custID = '$searchValue'";
            break;
        case 'paymentDuration':
            $query = "SELECT * FROM fitnessclassmember WHERE  paymentDuration = '$searchValue'";
            break;
        case 'fitnessType':
            $query = "SELECT * FROM fitnessclassmember WHERE  fitnessType = '$searchValue'";
            break;
        case 'joinDate':
            $query = "SELECT * FROM fitnessclassmember WHERE  joinDate = '$searchValue'";
            break;
        default:
            $query = "SELECT * FROM fitnessclassmember";
    }
} else {
    $query = "SELECT * FROM fitnessclassmember";
}
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Fitness Class Member</title>
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
            background 0.3s ease;
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
            <a href="userRequest.php" >User Request</a>
            <a href="classMember.php"class="active">Class Member</a>
            <a href="logout.php">Logout</a>
        </div>

        <h1>Fitness Class Member</h1>

        <div style="display:flex; justify-content: space-between; align-items: center;">

            <button class="add-button" onclick="openAddModal()">
                <img src="https://cdn-icons-png.flaticon.com/512/6335/6335606.png" alt="Add member"> Add
            </button>

            <button class="search-button" onclick="openSearchModal()">
                <img src="search-icon.png" alt="Search"> Search
            </button>

            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddModal()">&times;</span>
                    <h2>Add Member</h2>

                    <form method="POST" action="">
                        <label for="custID">Enter User ID:</label>
                        <input type="text" name="custID" placeholder="User ID" required><br>
                        <label for="paymentDuration">Select Duration:</label>
                        <select name="paymentDuration" required>
                            <option value="">Choose a duration</option>
                            <option value="1">1 Month</option>
                            <option value="3">3 Month</option>
                            <option value="6">6 Month</option>
                        </select>

                        <label for="fitnessType">Select Fitness Type:</label>
                        <select name="fitnessType" required>
                            <option value="">Choose a fitness type</option>
                            <option value="yoga">yoga</option>
                            <option value="gym">gym</option>
                        </select>

                        <input type="submit" name="add_member" value="Add Member">
                    </form>
                </div>
            </div>
        </div>

        <div id="searchModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSearchModal()">&times;</span>
        <h2>Search Fitness Member</h2>

        <form method="POST" action="">
            <label for="searchType">Search by:</label>
            <select name="searchType" id="searchType" required onchange="updateInputField()">
                <option value="">Select...</option>
                <option value="custID">Customer ID</option>
                <option value="paymentDuration">Payment Duration</option>
                <option value="fitnessType">Fitness Type</option>
                <option value="joinDate">Join Date</option>
            </select>

            <input type="text" id="searchValue" placeholder="Enter value" style="display: none;">

            <!-- Dropdown for fitness types -->
            <select name="searchValueDropdown" id="searchDropdown" style="display: none;">
                <option value="">Select Fitness Type...</option>
                <option value="yoga">Yoga</option>
                <option value="gym">Gym</option>
            </select>

            <!-- Dropdown for payment duration -->
            <select name="paymentDuration" id="paymentDuration" style="display: none;">
                <option value="">Select Duration...</option>
                <option value="1">1 Month</option>
                <option value="3">3 Months</option>
                <option value="6">6 Months</option>
            </select>

            <!-- Date input for joinDate -->
            <input type="date" name="joinDate" id="joinDate" style="display: none;">

            <input type="submit" name="search" value="Search">
        </form>
    </div>
</div>


        <table>
            <tr>
                <th>Member ID</th>
                <th>Customer ID</th>
                <th>Payment Duration</th>
                <th>Fitness Type</th>
                <th>Join Date</th>
                <th>Membership Expiry Date</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['memberID']); ?></td>
                    <td><?php echo htmlspecialchars($row['custID']); ?></td>
                    <td><?php echo htmlspecialchars($row['paymentDuration']); ?></td>
                    <td><?php echo htmlspecialchars($row['fitnessType']); ?></td>
                    <td><?php echo htmlspecialchars($row['joinDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['membershipExpiryDate']); ?></td> 
                    <td>

                        <button class="action-button update-button" onclick="openUpdateModal('<?php echo addslashes(htmlspecialchars($row['memberID'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['fitnessType'])); ?>', 
            '<?php echo addslashes(htmlspecialchars($row['paymentDuration'])); ?>')">Update
                        </button>
                        <a href="classMember.php?delete=<?php echo $row['memberID']; ?>"
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
            <h2>Update Member</h2>
            <form method="POST" action="">
                <input type="hidden" name="memberID" id="updatememberID">
                <label for="paymentDuration">Duration :</label>
                <select name="paymentDuration" id="updatepaymentDuration" required>
                            <option value="">Choose a Duration</option>
                            <option value="1">1 Month</option>
                            <option value="3">3 Month</option>
                            <option value="6">6 Month</option>
                </select><br>

                <label for="fitnessType">Fitness Type:</label>
                <select name="fitnessType" id="updatefitnessType" required onchange="checkStatus()">
                    <option value="">Choose a Fitness Type</option>
                    <option value="yoga">Yoga</option>
                    <option value="gym">Gym</option>
                </select><br>

                <input type="submit" name="update_member" value="Update Member">
                <button type="button" class="cancel-button" onclick="closeUpdateModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(memberID, fitnessType, paymentDuration) {
            document.getElementById('updatememberID').value = memberID;
            document.getElementById('updatefitnessType').value = fitnessType;
            document.getElementById('updatepaymentDuration').value = paymentDuration;
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
        var joinDateInput = document.getElementById("joinDate");
        var paymentDuration = document.getElementById("paymentDuration");

        
        searchValue.style.display = "none";
        searchDropdown.style.display = "none";
        joinDateInput.style.display = "none"; 
        paymentDuration.style.display = "none"; 

        // based on search type
        if (searchType === "fitnessType") {
            searchDropdown.style.display = "block";
            searchDropdown.setAttribute("name", "searchValue");
            searchValue.removeAttribute("name"); 
        } else if (searchType === "joinDate") {
            joinDateInput.style.display = "block";
            joinDateInput.setAttribute("name", "searchValue"); 
            searchValue.removeAttribute("name"); 
        } else if (searchType === "paymentDuration") {
            paymentDuration.style.display = "block";
            paymentDuration.setAttribute("name", "searchValue"); 
            searchValue.removeAttribute("name"); 
        } else  {
            searchValue.style.display = "block";
            searchValue.setAttribute("name", "searchValue"); 
            searchDropdown.removeAttribute("name");
        }
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
                    messageContainer.remove(); 
                }, 500); 
            }, 3000); 
        }

        window.onload = function () {
            <?php if ($success) { ?>
                showMessage('success', '<?php echo addslashes($success); ?>');
            <?php } elseif ($error) { ?>
                showMessage('error', '<?php echo addslashes($error); ?>');
            <?php } ?>
        };

    </script>
</body>

</html>