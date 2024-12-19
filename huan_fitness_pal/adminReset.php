<?php
session_start();
include("connection.php");

$adminEmail = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_verification'])) {
    $_SESSION['admin_verification_code'] = rand(1000, 9999); 
    $success = "Verification code sent! Simulated code: <strong>" . $_SESSION['admin_verification_code'] . "</strong>";
}

if (isset($_POST['verify_code'])) {
    $enteredCode = $_POST['admin_verification_code_input'];

    // If the entered code matches, retrieve admin username
    if ($enteredCode == $_SESSION['admin_verification_code']) {
        $query = "SELECT adminId FROM admin WHERE adminEmail = '$adminEmail'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $adminId = $row['adminId'];
            $verified = true;

            echo "<script>
                    showMessage('success', 'Verification successful! You can proceed.');
                  </script>";
        } else {
            $error = "Admin username not found!";
        }
    } else {
        $error = "Invalid verification code!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Verification</title>
    <link href="login-register.css" rel="stylesheet">
    <link href="reset.css" rel="stylesheet" >
    <style>
        p, .usernameDisplay{
            color: white;
            text-align: left;
            margin-left: 15px;
        }
        
        .send-verification{
            width: 100% !important;
        } 

        #message-container {
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%); /* Adjust position back to center */
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100px;
        }

        .message {
            width: 380px;
            min-height: 100px;
            display: flex;
            padding: 15px;
            margin: 5px;
            border-radius: 5px;
            color: white;
            opacity: 0;
            transition: opacity 0.5s;
            text-align: center;
            justify-content: center;
            align-items: center;
            min-width: 200px;
            align-self: center;
        }

        .message.success {
            padding-top: 65px;
            background-color: #d4edda; 
            color: #155724; 
        }

        .close-button {
            background: none;
            border: none;
            color: inherit; 
            font-size: 18px;
            cursor: pointer;
            float: right; 
            margin-left: 10px;
            margin-top:-70px;
        }


    </style>
</head>
<body>
<div class="background"></div>
<div class="logo-container">
    <img src="logo.png" alt="logo">
    <div class="container">
      <h2>Admin Password Reset Request</h2>
        <p>Admin Email: <strong><?php echo htmlspecialchars($adminEmail); ?></strong></p>
        
        <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>
        <?php if (!empty($success)) echo '<p class="success">' . $success . '</p>'; ?>

        <form id="sendVerificationCodeForm" method="POST">
            <input type="hidden" name="send_verification" value="1">
            <input class="send-verification" type="submit" value="Send Verification Code">
        </form>

        <form id="verifyCodeForm" method="POST">
            <div class="input-container">
                <input type="text" name="admin_verification_code_input" id="admin_verification_code_input" placeholder="Enter the verification code" required>
                <input type="hidden" name="verify_code" value="1">
                <input class="verify" type="submit" value="Verify Code">
            </div>
        </form>

        <div id="adminVerificationResult">
            <?php
            if (isset($verified) && $verified === true) {
                echo "<p class='success'>Code verified successfully!</p>";
                echo "<p>Admin Username: <strong>" . htmlspecialchars($adminId) . "</strong></p>";
            }
            ?>
        </div>
        
        <div id="usernameDisplay" style="display: none;">
            <p>Admin Username: <strong><?php echo htmlspecialchars($_SESSION['adminId']); ?></strong></p>
        </div>

        <input type="button" value="<< Back" onclick="location.href='loginRegister.php';">
        <input type="button" value="Submit" id="submit" onclick="handleSubmit()"<?php echo isset($verified) && $verified === true ? '' : 'disabled'; ?>>
        
        <div id="message-container"></div>
        
     </div>
</div>
<script>
    function showMessage(type, message) {
        const messageContainer = document.getElementById('message-container');
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('message', type);
    
        // Create close button
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.onclick = () => closeMessage(msgDiv); // Close specific message
        closeButton.className = 'close-button'; // Add a class for styling

        msgDiv.innerHTML = message; // Use innerHTML to allow for line breaks
        msgDiv.appendChild(closeButton); // Add close button to message div

        messageContainer.appendChild(msgDiv);
        msgDiv.style.display = 'block';
        msgDiv.style.opacity = '1';

        // Hide the message after 3 seconds
        setTimeout(() => {
            closeMessage(msgDiv);
        }, 3000); // Adjust timing as needed
    }


    function closeMessage(msgDiv) {
        const messageContainer = document.getElementById('message-container');
        messageContainer.removeChild(msgDiv); // Remove the specific message div
        if (messageContainer.children.length === 0) {
            window.location.href = 'loginRegister.php'; // Redirect if no messages are left
        }
    }


    function handleSubmit() {
        showMessage('success', '<strong>Request Submitted Successfully</strong><br>You will receive a new password within 24 hours.');
    }

</script>
</body>
</html>
