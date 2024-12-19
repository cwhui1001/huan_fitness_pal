<?php
session_start();
include("connection.php");

$error = '';

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['userid']) && isset($_POST['password'])) {
        $userid = $_POST['userid']; 
        $password = $_POST['password']; 

        // Check admin table
        $adminQuery = $conn->prepare("SELECT adminPsw FROM admin WHERE adminId = ?");
        $adminQuery->bind_param("s", $userid);
        $adminQuery->execute();
        $adminResult = $adminQuery->get_result();

        if ($adminResult->num_rows > 0) {
            // Fetch the hashed password
            $adminRow = $adminResult->fetch_assoc();
            $stored_password = $adminRow['adminPsw'];

            // Verify the password
            if ($password === $stored_password) {
                // Admin login successful
                $_SESSION['userid'] = $userid; 
                header('Location: admin_home.php'); 
                exit;
            } else {
                // Invalid password
                $error = 'Invalid User ID or Password!';
            }
        } else {
            // Check in the customer table
            $customerQuery = $conn->prepare("SELECT custPsw FROM customer WHERE custId = ?");
            $customerQuery->bind_param("s", $userid);
            $customerQuery->execute();
            $customerResult = $customerQuery->get_result();

            if ($customerResult->num_rows > 0) {
                // Fetch the hashed password
                $customerRow = $customerResult->fetch_assoc();
                $hashed_password = $customerRow['custPsw'];

                // Verify the password
                if (password_verify($password, $hashed_password)) {
                    // Customer login successful
                    $_SESSION['userid'] = $userid;  
                    header('Location: customer_home.php');  
                    exit;
                } else {
                    // Invalid password
                    $error = 'Invalid User ID or Password!';
                }
            } else {
                // Login failed, show error
                $error = 'Invalid User ID or Password!';
            }
        }
    } else {
        // Error if the form fields are missing
        $error = 'Please enter your User ID and Password!';
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="login-register.css" rel="stylesheet">
    <style>
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #dcd1f8;
        }

        .container {
            border: 2px solid white;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
        }

        .input-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 25px;
            padding: 5px;
            background-color: #f9f9f9;
            position: relative;
        }

        .input-container input[type="text"], 
        .input-container input[type="password"] {
            width: 100%;
            padding: 12px;
            padding-left: 40px;
            padding-right: 40px; 
            border: none;
            outline: none;
            background: transparent;
            font-size: 16px;
            border-radius: 25px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .input-container img {
            position: absolute;
            height:20px;
        }

        /* Lock icon on the left */
        .input-container img:first-of-type {
            left: 15px;
            pointer-events: none; 
        }

        .password-toggle {
            right: 15px;
            cursor: pointer;
            display: none;
            pointer-events: auto; 
        }

        input[type="submit"], input.back {
            width: 40%;
            padding: 12px;
            background: linear-gradient(145deg, #7a5dd6, #b297f7);
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background 0.3s ease;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        input[type="submit"]:hover {
            background: linear-gradient(145deg, #5d46a7, #7a5dd6);
        }

        input.back {
            color: #5d46a7;
            background: white;
        }
        input.back:hover {
            background: linear-gradient(145deg, #5d46a7, #7a5dd6);
            color: white;
        }
        a {
            color: white;
            text-decoration: none;
        }
        .forgot-password{
            height:20px;
            text-align: right;
            font-size: 10pt;
            padding-right: 8px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .forgot-password a:hover{
            text-decoration: underline;
        }
        
        .error {
            color: red;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            form input[type="text"],
            form input[type="password"] {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="background"></div>
<div class="logo-container">
    <img src="logo.png" alt="logo">
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>
        <form method="POST" action="">
            <div class="input-container">
                <img src="user.png" alt="User Icon">
                <input type="text" name="userid" placeholder="User ID" required>
            </div>
            <div class="input-container">
                <img src="lock.png" alt="Lock Icon">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <img class="password-toggle" src="hidden.png" id="togglePassword" alt="Password Visibility">
            </div>
            <div class="forgot-password">
                <a href="resetPassword.php">Forgot Password?</a>
            </div>
            <input class="back" type="button" value="<< Back" onclick="location.href='login-register.php';">
            <input type="submit" value="Login">
        </form>
    </div>
</div>
<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    //Show toggle password icon only when user typing
    passwordInput.addEventListener('input', function () {
        if (passwordInput.value.length > 0) {
            togglePassword.style.display = 'block';
        } else {
            togglePassword.style.display = 'none';
        }
    });

    //Toggle between showing and hiding password
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        //Change icon based on visibility
        if (type === 'password') {
            this.src = 'hidden.png';
        } else {
            this.src = 'show.png';
        }
    });
</script>
</body>
</html>
