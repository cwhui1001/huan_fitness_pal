<?php
include('connection.php');
session_start();  

if (!isset($_SESSION['userid'])) {
    header('Location: login.php');  // Redirect to login page if not logged in
    exit;
}

// Fetch admin details
$userid = mysqli_real_escape_string($conn, $_SESSION['userid']);
$query = "SELECT * FROM admin WHERE adminId = '$userid'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
} else {
    echo "No admin data found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="stylesheet" type="text/css" href="general.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            background: #f1f1f1;
            font-family: Arial, sans-serif;
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center; 
        }

        h1 {
            color: #484547;
            font-size: 28px;
        }

        h2 {
            color: #a34185;
            font-size: 28px;
        }

        h3 {
            color: #484547;
            font-size: 18px;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 0.8;
        }
        p {
            color: #4A4A4A;
            font-size: 14px;
        }

        /* Admin info section */
        .admin-frame {
            display: flex;
            align-items: center;
            padding: 20px;
            border-radius: 15px;
            background-color: #fbf5fa;
            max-width: 700px;
            margin: 20px auto;
        }

        .admin-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-left: 100px;
            margin-right: 50px;
            border: 2px solid #ddd;
        }

        .admin-info {
            text-align: left;
        }

        .admin-info p {
            color: #555;
            font-size: 18px;
            margin: 5px 0;
            font-weight: 500;
        }

        .cards {
            display: flex;
            justify-content: center; 
            gap: 80px; 
            margin-top: 20px; 
        }

        .card {
            width: 220px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f8ff;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 220px;
            margin-bottom: 10px;
            border-radius: 10px;
        }
        
        .card a {
            color: #6d5acf;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="admin_home.php" class="active">Home</a>
            <a href="userRequest.php">User Requests</a>
            <a href="classMember.php">Class Members</a>
            <a href="logout.php">Logout</a>
        </div>

        <h1>Welcome, <?php echo htmlspecialchars($admin['adminName']); ?>!</h1>  

        <div class="admin-frame">
        <img src="<?php echo htmlspecialchars($admin['adminPic']); ?>" alt="Admin Picture" class="admin-pic">
            <div class="admin-info">
                <h3><strong>Admin ID:</strong> <?php echo htmlspecialchars($admin['adminId']); ?></h3>
                <h3><strong>Name:</strong> <?php echo htmlspecialchars($admin['adminName']); ?></h3>
                <h3><strong>Email:</strong> <?php echo htmlspecialchars($admin['adminEmail']); ?></h3>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <a href="userRequest.php">
                    <img src="https://media.licdn.com/dms/image/D4D12AQGHf3cXB-a_Pw/article-cover_image-shrink_720_1280/0/1679989721915?e=2147483647&v=beta&t=gSRxaHbmONY15TSKarHDWWZEiSYzFeOHCL-OaXyQ2ns" alt="User Requests">
                    <h2>User Requests</h2>
                    <p>Consultantion Booking</p>
                </a>
            </div>
            <div class="card">
                <a href="classMember.php">
                    <img src="https://img.grouponcdn.com/metro_draft_service/4ZnKJMTjjQaVsV2xb4aMwJfNYDYa/4Z-1125x750/v1/t600x362.jpg" alt="Class Members">
                    <h2>Class Members</h2>
                    <p>Fitness Class Member</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>