<?php
session_start();

// Check if the user is already logged in, otherwise redirect to login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once '../config/db.php';

// Handle employee registration logic
if (isset($_POST['register_employee'])) {
    // Get form input values
    $employee_name = $_POST['employee_name'];
    $employee_role = $_POST['employee_role'];
    $employee_salary = $_POST['employee_salary'];
    $hire_date = $_POST['hire_date'];

    // Validate inputs (basic check)
    if (!empty($employee_name) && !empty($employee_role) && !empty($employee_salary) && !empty($hire_date)) {
        // Prepare the SQL query to insert the new employee into the database
        $query = "INSERT INTO employees (name, role, salary, hire_date) VALUES (?, ?, ?, ?)";

        // Use MySQLi prepared statements to prevent SQL injection
        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, 'ssds', $employee_name, $employee_role, $employee_salary, $hire_date);

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                // Set a success message
                $_SESSION['registration_message'] = "Employee registered successfully!";
            } else {
                // Set an error message
                $_SESSION['registration_message'] = "Error: " . mysqli_error($conn);
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['registration_message'] = "Error preparing the query.";
        }
    } else {
        // Set error message for missing fields
        $_SESSION['registration_message'] = "Please fill in all fields.";
    }

    // Redirect back to the registration page
    header("Location: register.php");
    exit();
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Employee</title>
    <link rel="stylesheet" href="../public/assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <h2>Register New Employee</h2>
        <form action="register.php" method="POST">
            <input type="text" name="employee_name" placeholder="Employee Name" required>
            <input type="text" name="employee_role" placeholder="Role" required>
            <input type="number" step="0.01" name="employee_salary" placeholder="Salary" required>
            <input type="date" name="hire_date" placeholder="Hire Date" required>
            <button type="submit" name="register_employee">Register</button>
        </form>

        <!-- Display success or error message if set -->
        <?php
        if (isset($_SESSION['registration_message'])) {
            echo "<p style='color: red; text-align: center;'>{$_SESSION['registration_message']}</p>";
            unset($_SESSION['registration_message']);
        }
        ?>
    </div>
</body>
</html>
