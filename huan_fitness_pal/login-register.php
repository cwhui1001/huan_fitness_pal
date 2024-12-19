<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link href="login.css" rel="stylesheet">
    <style>
        button {
            width: 40%;
            padding: 12px;
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background 0.3s ease;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .register{
          background: linear-gradient(145deg, #5d46a7, #7a5dd6);
        }
        .login{
          color: #5d46a7;
          background: white;
        }
        .register:hover {
            background: linear-gradient(145deg, #7a5dd6, #b297f7);
        }
        .login:hover {
            background: linear-gradient(145deg, #5d46a7, #7a5dd6);
            color: white;
        }

    </style>
</head>

<body>
<div class="background"></div>
<div class="logo-container">
    <img src="logo.png" alt="logo">
    <div class="container">
    <a href='login.php'> <button class="login" type="button">Login -></button></a>
    <a href='register.php'> <button class="register" type="button">Register -></button></a>
    </div>
</div>

</body>

</html>