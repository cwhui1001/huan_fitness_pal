<?php
    session_start();
    include('connection.php');
    
    // Check if the user is logged in
    if (!isset($_SESSION['userid'])) {
        header('Location: login.php');
        exit;
    }
    $custId = $_SESSION['userid']; 

    // Check if username is already set in the session
    if (!isset($_SESSION['username'])) {
        $query = "SELECT custName FROM customer WHERE custId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $custId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $custName);
        mysqli_stmt_fetch($stmt);

        // Store the username in the session
        $_SESSION['username'] = $custName;

        mysqli_stmt_close($stmt);
    }

    $weight = isset($weight) ? $weight : [];
    $recordDate = isset($recordDate) ? $recordDate : [];
    $category = isset($category) ? $category : [];
    $duration = isset($duration) ? $duration : [];
    $drinkingDate = isset($drinkingDate) ? $drinkingDate : [];
    $waterIntake = isset($waterIntake) ? $waterIntake : [];

    #Overview progress --chart.js
    // First SQL query for bodyweight 
    $sql = "SELECT * FROM bodyweight WHERE custId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt === false) {
        echo "Error preparing statement: " . mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $custId); // Bind the user ID
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt); // Get the result set
    
        // Check for results from the first query
        if (mysqli_num_rows($result) > 0) {
            // Fetch records from bodyweight
            $weight = array();
            $recordDate = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $weight[] = $row["weight"];
                $recordDate[] = $row["recordDate"];
            }
        } 
    
        // Free the result set
        mysqli_free_result($result);
        mysqli_stmt_close($stmt); // Close the statement
    }
    

    // Second SQL query for exerciseroutine
    $sql = "SELECT * FROM exerciseroutine WHERE custId = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo "Error preparing statement: " . mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $custId); // Bind the user ID
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt); // Get the result set

        // Fetch records from exerciseroutine
        if (mysqli_num_rows($result) > 0) {
            $category = array();
            $duration = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $category[] = $row["category"];
                $duration[] = $row["duration"];
            }
        } 

        // Free the result set
        mysqli_free_result($result);
        mysqli_stmt_close($stmt); // Close the statement
    }

    // Third SQL query for water consumption
    $sql = "SELECT * FROM waterconsumption WHERE custId = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo "Error preparing statement: " . mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $custId); // Bind the user ID
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt); // Get the result set

        // Fetch records from exerciseroutine
        if (mysqli_num_rows($result) > 0) {
            $drinkingDate = array();
            $waterIntake = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $drinkingDate[] = $row["drinkingDate"];
                $waterIntake[] = $row["waterIntake"];
            }
        } 
        // Free the result set
        mysqli_free_result($result);
        mysqli_stmt_close($stmt); // Close the statement
    }

    // Handle fitness class payment submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
       
        $custID = $_SESSION['userid']; 
        $paymentDuration = $_POST['duration'];
        $amountPaid = $_POST['amount'];
        $fitnessType = $_POST['fitnessType']; 
        $joinDate = date('Y-m-d');
        $expiryDate = date('Y-m-d', strtotime("+$paymentDuration months", strtotime($joinDate)));

        $query = "INSERT INTO fitnessclassmember (custID, paymentDuration, amountPaid, fitnessType, membershipExpiryDate) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            die("Error preparing the statement: " . mysqli_error($conn));
        }

        // Bind parameters
        if (!mysqli_stmt_bind_param($stmt, 'sssss', $custID, $paymentDuration, $amountPaid, $fitnessType, $expiryDate)) {
            die("Error binding parameters: " . mysqli_stmt_error($stmt));
        }

        // Execute the prepared statement
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error executing query: " . mysqli_stmt_error($stmt);
        } else {
            // Successful execution, redirect to payment success page
            header("Location: payment_success.php");
            exit;
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Customer Home</title>
    <link rel="stylesheet" type="text/css" href="general.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --viewport-height: 100vh;
        }

        html, body {
            height: 100vh; 
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;

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

        .container {
            display: flex;
            flex-direction: column;
        }

        .overview,
        .consultant_container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid */
            gap: 20px; 
        }

        h1 {
            margin-top: 20px;
            margin-bottom: 0;
            color: #333;
            font-size: 28px;
            padding: 20px;
        }

        p {
            color: #555;
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 30px;
            padding: 20px;
        }
        .title{
            padding: 20px;
            font-size: 30px;
            font-family: Georgia;
            font-weight: bold;
        }
        .overview{
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            align-items: center;
            gap: 20px;
            max-width: auto;
        }
        .chart{
            box-shadow: rgba(95, 41, 122, 0.5) 0px 4px 40px -10px;
            border-radius: 25px;
            padding: 30px;
            margin: 20px;
            height: 200px;
            display: flex; /* Enable flexbox */
            justify-content: center; /* Center items horizontally */
            align-items: center;
        }
        
        img{
            width: 1208px;
            margin-top: 400px;  
        }
        h2{
            padding: 20px;
            margin: 0;
        }
        .consultant_container{
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 50px;
            margin: 20px;
        }
        .consultant{
            box-shadow: rgba(95, 41, 122, 0.5) 0px 4px 40px -10px;
            border-radius: 25px;
            background: linear-gradient(to bottom, rgba(216, 191, 216, 0.8), rgba(143, 180, 225, 0.8));
            display: grid;
            grid-template-columns: 1fr 2fr;
            align-items: center;
            overflow-y: auto; /* Enable vertical scrolling */
            overflow-x: hidden; /* Prevent horizontal scrolling */
            width: 100%;
        } 
        .consultant-d{
            display: grid;
            grid-template-columns: 1fr 2fr;
            align-items: center;
        }
        button{
            padding: 0;
            text-align: start;
            border: none; 
            border-radius: 25px;
            cursor: pointer; 
            transition: background-color 0.3s ease; 
        }
        button:hover {
            transform: scale(1.1); 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
        }
        
        .a{
            width: 90px;
            height: 120px;
            margin: 0;
            padding: 20px;
            border-radius: 35px;
        }
        .b{
            width: 90px;
            height: 120px;
            margin: 0;
            margin-bottom: 10px;
            border-radius: 20px;
        }
        
        .profile{
            display: grid;
            grid-template-rows: auto;
        }
        .name,.position,.desc, .rating,.class-name{
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .name{
            font-size: 21px;
            font-weight: bold;
            padding: 10px;
            padding-left: 0;
            padding-top: 0;
            padding-right: 20px;
        }
        .position{
            color: darkblue;
            font-size: 12px; 
            font-weight: bold;
            padding-bottom: 10px;
            padding-right: 20px;
        }
        
        h4{
            font-weight: bold;
            padding: 0;
            margin: 0;
        }

        /* The Modal (background overlay) */
        .desc,.details {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); 
        }

        /* Modal Content */
        .desc-content {
            position: relative;
            background-color: #fff;
            margin: 5% auto; /* Center it vertically and horizontally */
            padding: 35px;
            border: 1px solid #888;
            width: 30%; /* Adjust the size as needed */
            max-height: 80vh;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            background: linear-gradient(to bottom, rgba(216, 191, 216, 1), rgba(143, 180, 225, 1));
            display: grid;
            grid-template-rows: auto ;
            overflow-y: auto; /* Enable vertical scrolling */
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        /* Close button (X) */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover {
            color: red;
        }
        .rating i {
            color: #ffcc00; /* Yellow color */
        }

        .fitness-container{
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Two columns */
            grid-gap: 20px; 
        }
        
        .class{
            box-shadow: rgba(102, 51, 204, 0.5) 0px 4px 40px -10px;
            border-radius: 25px;
            padding: 30px;
            margin: 20px;
            text-align: center;
            max-width: auto;
        }

        .pic{
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 0;
            padding: 0;
        }
        .class-name{
            font-size: 25px;
            font-weight: bold;
            padding: 10px;
            padding-top: 18px;
        }
        .benefits {
            list-style-type: none;
            padding: 0; 
            text-align: start; 
        }
        .benefits li{
            display: flex;
            align-items: center; 
            text-align: left;
            margin-bottom: 10px;
            padding-left: 0;
            font-size: 18px;
        }
        .benefits li::before {
            content: "✔"; /* Add the tick mark */
            color: rgba(102, 51, 204, 0.8); /* Set the color of the tick */
            font-weight: bold;
            margin-right: 10px; 
        }
        .class-benefits{
            display: flex;
            justify-content: center;
        }
        .view-details-btn, #appointment-btn, .pay-btn{
            background-color: #6c63ff; /* Primary color for the button */
            color: white; /* Button text color */
            padding: 12px 25px; /* Padding around the text */
            font-size: 16px; /* Text size */
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            border: none; /* Remove default borders */
            border-radius: 50px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Add subtle shadow */
            transition: all 0.3s ease; /* Smooth transitions for hover effects */
            outline: none; /* Remove outline */
        }

        .view-details-btn:hover,#appointment-btn:hover,.pay-btn:hover {
            background-color: #4c45e0; /* Darker shade of the primary color on hover */
            transform: translateY(-2px); /* Slight upward movement on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25); /* Larger shadow on hover */
        }

        .view-detail-btn:active,#appointment-btn:active,.pay-btn:active{
            background-color: #5548d8; /* Even darker shade when clicked */
            transform: translateY(-2px); /* Move up slightly on click */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Less shadow when clicked */
        }
        .a-btn{
            display: flex;
            justify-content: center;
        }

        /* Form element styling */
        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin: 0;
        }

        .details {
            display: none; /* Hidden initially */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background */
        }

        /* Modal Content */
        .details-content {
            position: relative;
            background-color: #fff;
            margin: 3% auto; /* Center it vertically and horizontally */
            padding: 35px;
            border: 1px solid #888;
            width: 63%; /* Adjust the size as needed */
            max-height: 90vh;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            background: linear-gradient(145deg, #f3e1f1, #d1c2e2);
            
            display: flex;           
            flex-wrap: wrap;
            flex-direction: row;      
            justify-content: space-between; 
            gap: 20px;                 
            overflow-y: auto; /* Enable vertical scrolling */
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        .content {
            display: flex;
            flex-wrap: wrap; /* Allows items to wrap into multiple rows */
            max-width: 1200px; 
            flex: 1 1 calc(33.333% - 20px); 
        }

        .y{
            padding: 15px;
            line-height: 25px;
            padding-top: 0;
            padding-bottom: 0;
        }

        .y-img{
            display: block;
            width: 320px; 
            height: 200px; 
            object-fit: cover;
            margin: 0;
            padding: 15px;
            border-radius: 20px;
            padding-top: 0;
            padding-bottom: 0;
        }
        h2.detail-title{
            padding: 0;
            padding-left: 15px;
        }

        label {
            display: block;
            color: black;
        }

        form input[type="number"], form input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px; 
            border: 1px solid #ccc;
            border-radius: 20px;
            transition: border-color 0.3s;
            box-sizing: border-box; 
        }
        
        form input[type="number"]:focus, form input[type="text"]:focus,
        select:focus {
            border-color: #4c45e0;
            outline: none;
            width: 100%;
        }

        .submit-container {
            text-align: center; 
            margin-top: 20px;
            width: 100%;
        }
        select {
            margin-top: 10px; 
        }
        
        #payment{
            margin: 20px;
            padding: 30px;
            height: auto;
            vertical-align: middle;
            max-width: 400px;
            width: 100%;
        }
       
        .form-group {
            margin-bottom: 15px;
            width: 100%;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 10px; 
        }

        .expiry-group,
        .cvv-group {
            flex: 1;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column; /* Stack navbar links vertically */
            }

            .container {
                padding: 10px; /* Adjust padding */
            }

            h1, p, h2 {
                padding: 10px;
                text-align: center; /* Center text on smaller screens */
            }

            .chart, .consultant {
                flex-direction: column; /* Stack items vertically */
                align-items: center; /* Center items */
            }

            img {
                width: 100%; /* Make images responsive */
                height: auto; /* Maintain aspect ratio */
            }
        
            .fitness-container {
                grid-template-columns: 1fr; /* Switch to one column on smaller screens */
            }

            .overview,
            .consultant_container {
                grid-template-columns: 1fr; /* Stack items vertically on smaller screens */
            }
            
            .class {
                flex: 1 1 calc(100% - 20px); /* Full width on smaller screens */
            }
        }
    </style>
    <script>
        //Body Weight Progress Overview
        //setup block
        document.addEventListener('DOMContentLoaded', function () {
            const weight = <?= json_encode($weight)?>;
            const recordDate = <?= json_encode($recordDate)?>;
            const data = {
                labels: recordDate,
                datasets: [{
                    label: 'Body Weight (kg)',
                    data: weight,
                    fill: false,
                    borderColor: 'rgba(128, 0, 128, 0.4)',
                    tension: 0.1
                }]
            };
            const config = {
                type: 'line',
                data: data,
                options: {
                    maintainAspectRatio: false,
                }
            };
            //Render Block
            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            )    
        });

        //Exercise Routine Progress Overview
        document.addEventListener('DOMContentLoaded', function () {
            const category = <?= json_encode($category)?>;
            const duration = <?= json_encode($duration)?>;
            const data2 = {
                labels: category.length > 0 ? category : ['No Record'],
                datasets: [{
                    label: 'Duration (mins)',
                    data: duration.length > 0 ? duration : [1], // Set a single value to show an empty doughnut
                    backgroundColor: [
                        'rgb(255, 99, 132)', // red
                        'rgb(54, 162, 235)', // blue
                        'rgb(255, 205, 86)', // yellow
                        'rgba(255, 20, 147, 0.8)', // pink
                        'rgba(147, 112, 219, 0.8)', // purple
                        'rgba(0, 128, 0, 0.7)', // green
                        'rgb(255, 165, 0)', // orange
                        'rgba(0, 255, 255, 0.8)', // cyan
                        'rgba(144, 238, 144, 0.8)', // light green
                        'rgba(0, 128, 128, 0.8)', // teal
                        'rgba(165, 42, 42, 0.8)', // brown
                        'rgba(112, 128, 144, 0.8)', // grey
                        'rgba(173, 216, 230, 0.8)' // light blue
                    ],
                    hoverOffset: 15
                }]
            };

            const config2 = {
                type: 'doughnut',
                data: data2,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right' 
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + (context.raw === 0 ? 'No record' : context.raw);
                                }
                            }
                        }
                    }
                }
            };
            const myChart2 = new Chart(
                document.getElementById('myChart2'),
                config2
            )    
        });

        //Water Consumption
        document.addEventListener('DOMContentLoaded', function () {
            const drinkingDate = <?= json_encode($drinkingDate)?>;
            const waterIntake = <?= json_encode($waterIntake)?>;
            const data3 = {
                labels: drinkingDate,
                datasets: [{
                    label: 'Water Consumption (ml)',
                    data: waterIntake,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgb(54, 162, 235)'
                    ],
                    borderWidth: 1
                }]
            };

            const config3 = {
                type: 'bar',
                data: data3,
                options: {
                    maintainAspectRatio:false,
                    scale: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            const myChart3 = new Chart(
                document.getElementById('myChart3'),
                config3
            )
        });

        // Update the amount based on selected duration for both Yoga and Gym
        function updateAmount(className) {
            const duration = document.getElementById('duration-' + className).value;
            const amountField = document.getElementById('amount-' + className);
            let amount = 0;
            switch (duration) {
                case '1':
                    amount = 50;  
                    break;
                case '3':
                    amount = 135; 
                    break;
                case '6':
                    amount = 240; 
                    break;
            }
            if (amountField) {
                amountField.value = amount;
            } else {
                console.error('Amount field not found for class:', className);
            }
        }
        
        //Payment validation
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            
            let formattedValue = '';
            for (let i = 0; i < value.length; i += 4) {
                if (i > 0) {
                    formattedValue += ' ';
                }
                formattedValue += value.substring(i, i + 4);
            }
            input.value = formattedValue;
        }

        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 4) {
                value = value.slice(0, 4);
            }

            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }

            input.value = value;
        }

        function validateExpiryDate(className) {
            const expiryInput = document.getElementById(`expiryDate-${className}`).value;
            console.log(`Expiry Input for ${className}:`, expiryInput); // Log to confirm

            const monthYear = expiryInput.split('/');
            if (monthYear.length !== 2) {
                alert('Please enter a valid expiry date in MM/YY format.');
                return false;
            }

            const month = parseInt(monthYear[0], 10);
            const year = parseInt(monthYear[1], 10);
            if (month < 1 || month > 12) {
                alert('Please enter a valid month (01-12).');
                return false;
            }

            const currentYear = new Date().getFullYear() % 100;
            if (year < currentYear || (year === currentYear && month < new Date().getMonth() + 1)) {
                alert('The expiry date cannot be in the past.');
                return false;
            }

            return true; // Allow form submission if date is valid
        }

    </script>
</head>

<body>
    <div class="container">
    <div class="navbar">
        <a href="customer_home.php" class="active">Home</a>
        <a href="bodyWeight.php">Body Weight</a>
        <a href="exerciseRoutine.php">Exercise Routine</a>
        <a href="waterConsumption.php">Water Consumption</a>
        <a href="bookingConsultant.php">Booking Consultant</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1> 
    
    <!-- <h2>Overview</h2> -->
    <div class="overview">
        <div class="chart">
            <canvas id="myChart"></canvas>
        </div>
        <div class="chart">
            <canvas id="myChart2"></canvas>
        </div>
        <div class="chart">
            <canvas id="myChart3"></canvas>
        </div>
    </div>
   
    <br>

    <h2>Meet Our Consultant</h2>
    <div class="consultant_container">

     <!-- Fetch consultants data -->
    <?php
        $sql = "SELECT * FROM consultants";
        $result = $conn->query($sql);

        // Check if we got results
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $name = $row['name'];
                $position = $row['position'];
                $rating = $row['rating'];
                $profile_image_url = $row['profile_image_url'];
                $background = $row['background'];
                $specialization = $row['specialization'];
                $philosophy = $row['philosophy'];
                $achievements = $row['achievements'];
                $safeName = strtolower(str_replace(" ", "_", $name));

                //Consultants
                echo '
                    <button id="viewprofile_'.$safeName.'">
                    <div class="consultant">
                        <div class="con"><img class="a" src="'.$profile_image_url.'"></div>
                        <div class="profile">
                            <div class="name">'.$name.'</div>
                            <div class="position">'.$position.'</div>
                            <div class="rating">'.$rating.' <i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </button>
                
                <div id="popup_'.$safeName.'" class="desc">
                <div class="desc-content">
                    <span class="close-btn">&times;</span>
                    <div class="consultant-d">
                        <div class="con"><img class="b" src="'.$profile_image_url.'"></div>
                        <div class="profile">
                            <div class="name">'.$name.'</div>
                            <div class="position">'.$position.'</div>
                            <div class="rating">'.$rating.' <i class="fas fa-star"></i></div>
                        </div>
                    </div><br>
                    <div>
                        <h4>Background:</h4> 
                        '.$background.'                    
                        </div><br>
                    <div>
                        <h4>Specialization:</h4>
                        '.$specialization.'
                    </div><br>
                    <div>
                        <h4>Philosophy:</h4> 
                        '.$philosophy.'
                        </div><br>
                    <div>
                        <h4>Achievements:</h4>
                        '.$achievements.'
                    </div> <br><br>
                    <div class="a-btn">
                        <a href="bookingConsultant.php">
                            <button id="appointment-btn">Book Appointment Now!</button>
                        </a>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo "<p>No consultants found.</p>";
    } 
    ?>
    </div>
    <br><br>

    <h2>Join Our Fitness Class</h2>
    <div class="fitness-container">
        <?php
        // SQL query to fetch fitness classes
        $sql = "SELECT * FROM fitnessclass";
        $result = $conn->query($sql);

        // Check if we got results
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $class_name = $row['class_name'];
                $image_url = $row['image_url'];
                $benefits = $row['benefits'];
                $description = $row['description'];
                $schedule = $row['schedule'];
                $location = $row['location'];
                $spots_available = $row['spots_available'];
                $safeClassName = strtolower(str_replace(" ", "_", $class_name));
        ?>
        <div class="class">
            <div><img class="pic" src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($class_name) ?>"></div>
            <div class="class-name"><?= htmlspecialchars($class_name) ?></div>
            <div class="class-benefits">
                <?php 
                    // Ensure benefits are displayed as a list
                    $benefitArray = explode(', ', $benefits);
                    echo '<ul class="benefits">';
                    foreach ($benefitArray as $benefit) {
                        echo '<li>' . htmlspecialchars($benefit) . '</li>';
                    }
                    echo '</ul>';
                ?>
            </div>
            <div>
                <button id="view-details-<?= $safeClassName ?>" class="view-details-btn" onclick="openModal('<?= $safeClassName ?>'); setFitnessType('<?= $safeClassName ?>')">View Details</button>
            </div>
        </div>
        
         <!-- Modal for the class -->
         <div id="popup_<?= $safeClassName ?>" class="details" style="display:none;">
            <div class="details-content">
                <span class="close-btn" onclick="closeModal('<?= $safeClassName ?>')">&times;</span>
                <div class="content">
                    <h2 class="detail-title"><?= htmlspecialchars($class_name) ?></h2>
                    <div><img class="y-img" src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($class_name) ?>"></div>
                    <div class="y"><?= htmlspecialchars($description) ?></div>
                    <div class="y"><strong>Schedule:</strong> <?= htmlspecialchars($schedule) ?></div>
                    <div class="y"><strong>Location:</strong> <?= htmlspecialchars($location) ?></div>
                    <div class="y"><strong>Spots Available:</strong> <?= htmlspecialchars($spots_available) ?></div>
                </div>
                <div class="content">
                    <form id="payment-<?= $safeClassName ?>" action="customer_home.php" method="POST" onsubmit="return validateExpiryDate('<?= $safeClassName ?>');">
                        <input type="hidden" name="submit_payment" value="<?= $row['id'] ?>">
                        <input type="hidden" id="fitnessType-<?= $safeClassName ?>" name="fitnessType" value="<?= $class_name ?>">

                        <div class="form-group">
                            <label for="duration-<?=$safeClassName?>">Select Payment Duration</label>
                            <select id="duration-<?= $safeClassName ?>" name="duration" required onchange="updateAmount('<?= $safeClassName ?>')">
                                <option value="" disabled selected>Select duration</option>
                                <option value="1">1 Month</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                            </select>
                            <br><br>
                            <label for="amount-<?= $safeClassName ?>">Amount (RM)</label>
                            <input type="number" class="pay-amount" id="amount-<?= $safeClassName ?>"  name="amount" required readonly>
                        </div>

                        <div class="form-group">
                            <label for="cardholder-name">Cardholder's Name:</label>
                            <input type="text" id="cardholder-name-<?= $safeClassName ?>" name="cardholder-name" placeholder="John Doe" required>
                        </div>

                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" id="cardNumber-<?= $safeClassName ?>" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required pattern="\d{4} \d{4} \d{4} \d{4}" oninput="formatCardNumber(this)" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group expiry-group">
                                <label for="expiryDate">Expiry Date</label>
                                <input type="text" id="expiryDate-<?= $safeClassName ?>" name="expiryDate" placeholder="MM/YY" maxlength="5" required oninput="formatExpiryDate(this)" required>
                            </div>
                            <div class="form-group cvv-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv-<?= $safeClassName ?>" name="cvv" placeholder="123" maxlength="3" required pattern="\d{3}" required>
                            </div>
                        </div>

                        <div class="submit-container">
                            <button id="pay-btn-<?= $safeClassName ?>" class="pay-btn" type="submit">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            } // End while loop
        } else {
            echo "<p>No Class found.</p>";
        }
        ?>
    </div>

    <script>
        // Function to set fitness type
        function setFitnessType(type) {
            const simpleType = type.includes('gym') ? 'gym' : 'yoga'; // Adjust for other classes as needed
            document.getElementById("fitnessType-" + type).value = simpleType;
        }
        function openModal(classType) {
            setFitnessType(classType); // Set the class type for this session
            document.getElementById("popup_" + classType).style.display = "block"; // Show your modal
        }

        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                this.closest('.desc').style.display = 'none';
                this.closest('.details').style.display = 'none';
            });
        });
        // Add this part to close modal on clicking outside for all modals
        document.querySelectorAll('.desc').forEach(modal => {
            modal.addEventListener('click', function(event) {
                // Close modal if clicking on the background (not the content)
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('button[id^="viewprofile_"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const popupId = 'popup_' + this.id.replace('viewprofile_', '');
                document.getElementById(popupId).style.display = 'block';
            });
        });

        function openModal(class_name) {
            document.getElementById("popup_" + class_name).style.display = "block";
            // Add event listener to close modal when clicking outside of the content
            const modal = document.getElementById("popup_" + class_name);
            modal.addEventListener('click', function(event) {
                // Close modal if clicking on the background (not the content)
                if (event.target === modal) {
                    closeModal(class_name);
                }
            });
        }

        function closeModal(class_name) {
            document.getElementById("popup_" + class_name).style.display = "none";
        }

        function showSuccessModal() {
            document.getElementById("payment-success-popup").style.display = "block";
        }

        function closeSuccessModal() {
            document.getElementById("payment-success-popup").style.display = "none";
        }
    </script>
</body>
</html>
