<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Report</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #fff; /* White background */
            color: #000; /* Black text */
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
        }
        .salary-report {
            background-color: #000; /* Black background */
            color: #fff; /* White text */
            padding: 20px;
            border-radius: 10px;
        }
        /* Add more styles as needed */
    </style>
</head>
<body>
<div class="container">
    <?php
    require_once('Salaryreport.php');

    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $database = "RouteReady";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $salaryReport = new SalaryReport($connection);

    if (isset($_GET['driver_id'])) {
        $driver_id = $_GET['driver_id'];
        $salary_data = $salaryReport->getSalaryData($driver_id);
        $bank_data = $salaryReport->getBankData($driver_id);

        if ($salary_data) {
            echo "<div class='salary-report'>";
            echo "<h2>Salary Report</h2>";
            echo "<h3>Driver ID: $driver_id</h3>";
            echo "<hr>";
            echo "<h3>Salary Details:</h3>";
            echo "<p>Basic Salary: " . $salary_data['basic_salary'] . "</p>";
            echo "<p>Number of Days Worked: " . $salary_data['number_of_days'] . "</p>";
            echo "<p>Additional Deduction Days: " . $salary_data['additional_deduction_days'] . "</p>";
            echo "<p>Service Commission: " . $salary_data['service_commission'] . "</p>";
            echo "<p>Total Salary: " . $salary_data['total_salary'] . "</p>";

            echo "<h3>Bank Details:</h3>";
            if ($bank_data) {
                echo "<p>Account Number: " . $bank_data['accountNo'] . "</p>";
                echo "<p>Bank Name: " . $bank_data['bankName'] . "</p>";
                echo "<p>Branch Name: " . $bank_data['branchName'] . "</p>";
                echo "<p>Holder's Name: " . $bank_data['holdersName'] . "</p>";
            } else {
                echo "<p>No bank details found for Driver ID: $driver_id</p>";
            }

            echo "</div>"; // Close salary-report div
        } else {
            echo "<p>No salary data found for Driver ID: $driver_id</p>";
        }
    }

    // Close the database connection
    $connection->close();
    ?>
</div>
</body>
</html>
