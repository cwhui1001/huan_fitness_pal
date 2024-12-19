<?php
session_start();
include("connection.php");

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}   

// Check if the registration form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $error_messages = []; 

    if (isset($_POST['userid']) && isset($_POST['password']) && isset($_POST['name']) && isset($_POST['bdate']) && isset($_POST['email'])) {
        $userid = $_POST['userid'];
        $name = $_POST['name'];
        $bdate = $_POST['bdate'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!preg_match('/^[a-zA-Z0-9]{5,15}$/', $userid)) {
            $error_messages['userid'] = '*Username must be 5-15 characters without symbols.';
        }

        if (!preg_match('/^[a-zA-Z\s]{3,70}$/', $name)) {
            $error_messages['name'] = '*Name must be 3-70 characters long and contain only letters.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_messages['email'] = '*Invalid email format.';
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $error_messages['password'] = '*Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.';
        }

        // Check if username already exists
        if (empty($error_messages)) {
            $usernameCheckQuery = mysqli_prepare($conn, "SELECT * FROM customer WHERE custId = ?");
            mysqli_stmt_bind_param($usernameCheckQuery, "s", $userid);
            mysqli_stmt_execute($usernameCheckQuery);
            $usernameResult = mysqli_stmt_get_result($usernameCheckQuery);

            if (mysqli_num_rows($usernameResult) > 0) {
                $error_messages['userid'] = 'Username taken. Please enter another username.';
            }
            mysqli_stmt_close($usernameCheckQuery);

            // Check if email already exists
            $emailCheckQuery = mysqli_prepare($conn, "SELECT * FROM customer WHERE custEmail = ?");
            mysqli_stmt_bind_param($emailCheckQuery, "s", $email);
            mysqli_stmt_execute($emailCheckQuery);
            $emailResult = mysqli_stmt_get_result($emailCheckQuery);

            if (mysqli_num_rows($emailResult) > 0) {
                $error_messages['email'] = 'Email already registered. Please use another email.';
            }
            mysqli_stmt_close($emailCheckQuery);
        }


        // If there are no errors, proceed to registration
        if (empty($error_messages)) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO customer (custId, custName, custBdate, custEmail, custPsw) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $userid, $name, $bdate, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                // Registration successful
                $_SESSION['userid'] = $userid;  // Save user ID
                header('Location: customer_home.php'); 
                exit;
            } else {
                // Registration failed, show error
                $error_messages['form'] = 'Registration failed! Please try again.';
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        $error_messages['form'] = 'Please fill in all required fields!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="login-register.css" rel="stylesheet">
    <style>
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #dcd1f8;
        }

        .main-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 40vh;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .container {
            border: 2px solid white;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            padding: 20px;
            width: 400px;
            margin-left: 20px;
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
        .input-container input[type="date"],
        .input-container input[type="email"],
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

        .input-container input[type="date"] {
            padding-right: 11px; 
        }
        .input-container img {
            position: absolute;
            height: 20px;
        }

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
            margin-top: 20px;
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

        .error {
            color: red;
            margin-bottom: 10px;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            form input[type="text"],
            form input[type="password"] {
                padding: 10px;
            }
        }
        @media (max-width: 1000px) {
            .main-container {
                display: flex;
                flex-direction: column;
                align-items: center; 
                margin-right: 0;
                margin-top: -420px;
            }
            .container {
                width: 100%; 
                margin-left: 0; 
            }
            .background {
                position: fixed;
                object-fit: cover;
            }
        }
    </style>
</head>
<body>
<div class="background"></div>
    <div class="main-container">
        <img src="logo.png" alt="logo">
        <div class="container">
            <h2>Register</h2>
            <p class="error" id="userid-error"><?php echo isset($error_messages['userid']) ? $error_messages['userid'] : ''; ?></p>
            <p class="error" id="name-error"><?php echo isset($error_messages['name']) ? $error_messages['name'] : ''; ?></p>
            <p class="error" id="email-error"><?php echo isset($error_messages['email']) ? $error_messages['email'] : ''; ?></p>
            <p class="error" id="password-error"><?php echo isset($error_messages['password']) ? $error_messages['password'] : ''; ?></p>


            <form method="POST" action="">
                <div class="input-container">
                    <img src="user.png" alt="Username Icon">
                    <input type="text" name="userid" id="userid" placeholder="Username" required>
                </div>
                <div class="input-container">
                    <img src="user.png" alt="Name Icon">
                    <input type="text" name="name" id="name" placeholder="Name" required>
                </div>
                <div class="input-container">
                    <img src="bdate.png" alt="Date Icon">
                    <input type="date" name="bdate" id="bdate" placeholder="Date of Birth" max="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="input-container">
                    <img src="email.png" alt="Email Icon">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <img src="lock.png" alt="Lock Icon">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <img class="password-toggle" src="hidden.png" id="togglePassword" alt="Password Visibility">
                </div>
                <input class="back" type="button" value="<< Back" onclick="location.href='login-register.php';">
                <input type="submit" value="Register">
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input');
    
            // Function to validate each input and show errors dynamically
            function validateInput(input) {
                let errorMsg = '';
                const errorDisplay = document.getElementById(`${input.id}-error`);

                errorDisplay.style.display = 'none';

                if (input.id === 'userid') {
                    const userIdPattern = /^[a-zA-Z0-9]{5,15}$/;
                    if (!userIdPattern.test(input.value)) {
                        errorMsg = '*Username must be 5-15 characters without symbols.';
                    } else if (input.value.trim() !== '') {
                        // Check username availability only if the input is not empty
                        checkUsernameAvailability(input.value);
                    }
                }
            

                if (input.id === 'name') {
                    const namePattern = /^[a-zA-Z\s]{3,70}$/;
                    if (!namePattern.test(input.value)) {
                        errorMsg = '*Name must be 3-70 characters long and contain only letters.';
                    }
                }

                if (input.id === 'email') {
                    if (!filterEmail(input.value)) {
                        errorMsg = '*Invalid email format.';
                    } else if (input.value.trim() !== '') {
                        // Check email availability only if the input is not empty
                        checkEmailAvailability(input.value);
                    }
                }

                if (input.id === 'password') {
                    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                    if (!passwordPattern.test(input.value)) {
                        errorMsg = '*Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.';
                    }
                }

                if (errorMsg) {
                    errorDisplay.textContent = errorMsg;
                    errorDisplay.style.display = 'block';
                } else {
                    errorDisplay.style.display = 'none'; // Hide error if valid
                }
            }

    function filterEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function checkUsernameAvailability(username) {
        fetch('check_availability.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'userid=' + encodeURIComponent(username),
        })
        .then(response => response.text())
        .then(data => {
            const errorDisplay = document.getElementById('userid-error');
            if (data === 'taken') {
                errorDisplay.textContent = 'Username taken. Please enter another username.';
                errorDisplay.style.display = 'block';
            } else {
                errorDisplay.style.display = 'none'; // Hide error if valid
            }
        });
    }

    function checkEmailAvailability(email) {
        fetch('check_availability.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email),
        })
        .then(response => response.text())
        .then(data => {
            const errorDisplay = document.getElementById('email-error');
            if (data === 'taken') {
                errorDisplay.textContent = 'Email already registered. Please use another email.';
                errorDisplay.style.display = 'block';
            } else {
                errorDisplay.style.display = 'none'; // Hide error if valid
            }
        });
    }

    inputs.forEach(input => {
        // Show error on focus and validate
        input.addEventListener('focus', () => {
            const errorDisplay = document.getElementById(`${input.id}-error`);
            if (errorDisplay.textContent.trim() !== '') {
                errorDisplay.style.display = 'block'; // Show existing error on focus
            }
        });

        // Validate input on input event
        input.addEventListener('input', () => {
            validateInput(input); // Validate on input
        });

        // Hide error on input change if valid
        input.addEventListener('blur', () => {
            if (input.value) {
                validateInput(input); // Validate on blur
            }
        });
    });
});

    </script>
</body>
</html>