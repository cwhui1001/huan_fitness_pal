<?php
session_start();
include("connection.php");

// Handle AJAX verification request
if (isset($_POST['ajax']) && $_POST['ajax'] == 'verify_code') {
    $entered_code = $_POST['reset_code'];

    // Check if the entered code matches the session code
    if ($entered_code == $_SESSION['reset_code']) {
        echo "success";
    } else {
        echo "Invalid reset code!";
    }
    exit(); // End script to prevent further code execution for AJAX
}

// Regular form processing if not AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $adminEmail = $_POST['admin_email'];

        // Check if the email exists in the customer table
        $query_customer = "SELECT * FROM customer WHERE custEmail = '$email'";
        $result_customer = mysqli_query($conn, $query_customer);

        // Check if the email exists in the admin table
        $query_admin = "SELECT * FROM admin WHERE adminEmail = '$email'";
        $result_admin = mysqli_query($conn, $query_admin);

        if (mysqli_num_rows($result_admin) > 0) {
            $adminData = mysqli_fetch_assoc($result_admin);
            $_SESSION['admin_email'] = $adminData['adminEmail'];
            $_SESSION['admin_id'] = $adminData['adminId'];  // Set adminId as well
            header("Location: adminReset.php");
            exit();
        } elseif (mysqli_num_rows($result_customer) > 0) {
            // Process customer reset code
            $_SESSION['email'] = $email;
            $_SESSION['reset_code'] = rand(1000, 9999); // Generate a 4-digit random code

            // Send the reset code
            $success = "A reset code has been sent to your email.<br>(Simulated code: " . $_SESSION['reset_code'] . ")";
        } else {
            $error = "Email not found! <a href='register.php'>Register</a>";
        }
    }

    if (isset($_POST['reset_code']) && isset($_POST['new_password'])) {
        $entered_code = $_POST['reset_code'];
        $new_password = $_POST['new_password'];

        if ($entered_code == $_SESSION['reset_code']) {
            // Update the password in the customer table
            $email = $_SESSION['email'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); 

            $query_customer = "UPDATE customer SET custPsw = '$hashed_password' WHERE custEmail = '$email'";
            mysqli_query($conn, $query_customer);

            // Clear session variables after reset
            unset($_SESSION['email']);
            unset($_SESSION['reset_code']);

            $success = "Your password has been successfully reset!<br><a href='login.php'>Login here</a>";
        } else {
            $error = 'Invalid reset code!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="login-register.css" rel="stylesheet">
    <link href="reset.css" rel="stylesheet" >
</head>
<body>
<div class="background"></div>
<div class="logo-container">
    <img src="logo.png" alt="logo">
    <div class="container">
        <h2>Reset Password</h2>

        <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>
        <?php if (!empty($success)) echo '<p class="success">' . $success . '</p>'; ?>
        <div id="verificationResult"></div>
        <div id="passwordError" class="error"></div>

        <form method="POST" action="">
            <div class="input-container">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <input type="submit" value="Send Reset Code">
        </form>

        <form method="POST" action="" id="passwordResetForm">
            <div class="input-container">
                <input type="text" name="reset_code" id="reset_code" placeholder="Enter the reset code" required>
                <input class="verify" type="button" value="Verify" onclick="verifyCode()">
            </div>
            <div class="input-container">
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required disabled>
            </div>
            <input class="back" type="button" value="<< Back" onclick="location.href='login.php';">
            <input type="submit" value="Reset Password">
        </form>
    </div>
</div>
<script>
    function verifyCode() {
    const enteredCode = document.getElementById("reset_code").value;
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true); 
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    // Hide the simulated code message before showing the verification result
    document.querySelector('.success').innerHTML = "";

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText.trim() === "success") {
                document.getElementById("verificationResult").innerHTML = "<p class='success'>Code verified successfully!</p>";
                document.getElementById("new_password").disabled = false;
            } else {
                document.getElementById("verificationResult").innerHTML = "<p class='error'>" + xhr.responseText + "</p>";
            }
        }
    };
    xhr.send("ajax=verify_code&reset_code=" + enteredCode);
}

/// Function to validate password format
function validatePassword() {
    const password = document.getElementById("new_password").value;
    const passwordError = document.getElementById("passwordError");
    const verificationResult = document.getElementById("verificationResult");
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    // Hide verification result message
    verificationResult.innerHTML = "";

    // Validate password and show error if invalid
    if (!regex.test(password)) {
        passwordError.innerHTML = "Password must be at least 8 characters, containing at least 1 uppercase, 1 lowercase, 1 number, and 1 special character.";
        return false;
    } else {
        passwordError.innerHTML = "";
        return true;
    }
}

// Attach validation to only the password reset form
document.getElementById("passwordResetForm").onsubmit = function(event) {
    if (!validatePassword()) {
        event.preventDefault(); // Prevent form submission if password is invalid
    }
};


// Attach validation to only the password reset form
document.getElementById("passwordResetForm").onsubmit = function(event) {
    if (!validatePassword()) {
        event.preventDefault(); // Prevent form submission if password is invalid
    }
};

</script>
</body>
</html>
