<?php
session_start();
require_once '../config/db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle employee registration logic
if (isset($_POST['register_employee'])) {
    $employee_name = $_POST['employee_name'];
    $employee_role = $_POST['employee_role'];
    $employee_salary = $_POST['employee_salary'];
    $hire_date = $_POST['hire_date'];

    if (!empty($employee_name) && !empty($employee_role) && !empty($employee_salary) && !empty($hire_date)) {
        $query = "INSERT INTO employees (name, role, salary, hire_date) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'ssds', $employee_name, $employee_role, $employee_salary, $hire_date);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['registration_message'] = "Employee registered successfully!";
            } else {
                $_SESSION['registration_message'] = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['registration_message'] = "Error preparing the query.";
        }
    } else {
        $_SESSION['registration_message'] = "Please fill in all fields.";
    }

    header("Location: register.php");
    exit();
}

// Handle employee search logic
$search_query = "";
$search_value = "";
$search_type = "name";

if (isset($_POST['search'])) {
    $search_value = $_POST['search_value'];
    $search_type = $_POST['search_type'];

    $search_query = " WHERE 1=1";
    if (!empty($search_value)) {
        $search_query .= " AND " . $search_type . " LIKE '%" . mysqli_real_escape_string($conn, $search_value) . "%'";
    }
}

// Fetch employees with search filters
$query = "SELECT * FROM employees" . $search_query;
$result = mysqli_query($conn, $query);

// Handle delete employee
if (isset($_GET['delete_id'])) {
    $employee_id = $_GET['delete_id'];

    $delete_query = "DELETE FROM employees WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $delete_query)) {
        mysqli_stmt_bind_param($stmt, 'i', $employee_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['registration_message'] = "Employee deleted successfully!";
        } else {
            $_SESSION['registration_message'] = "Error deleting employee: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: register.php");
    exit();
}

// Handle update employee
if (isset($_GET['edit_id'])) {
    $employee_id = $_GET['edit_id'];
    $edit_query = "SELECT * FROM employees WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $edit_query)) {
        mysqli_stmt_bind_param($stmt, 'i', $employee_id);
        mysqli_stmt_execute($stmt);
        $result_edit = mysqli_stmt_get_result($stmt);
        $employee_data = mysqli_fetch_assoc($result_edit);
        mysqli_stmt_close($stmt);
    }
}

// Handle update form submission
if (isset($_POST['update_employee'])) {
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $employee_role = $_POST['employee_role'];
    $employee_salary = $_POST['employee_salary'];
    $hire_date = $_POST['hire_date'];

    if (!empty($employee_name) && !empty($employee_role) && !empty($employee_salary) && !empty($hire_date)) {
        $update_query = "UPDATE employees SET name = ?, role = ?, salary = ?, hire_date = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $update_query)) {
            mysqli_stmt_bind_param($stmt, 'ssdsd', $employee_name, $employee_role, $employee_salary, $hire_date, $employee_id);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['registration_message'] = "Employee updated successfully!";
            } else {
                $_SESSION['registration_message'] = "Error updating employee: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $_SESSION['registration_message'] = "Please fill in all fields.";
    }
    header("Location: register.php");
    exit();
}

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
            <input type="date" name="hire_date" value="<?php echo date('Y-m-d'); ?>" required>
            <button type="submit" name="register_employee">Register</button>
        </form>

        <?php
        if (isset($_SESSION['registration_message'])) {
            echo "<p style='color: red; text-align: center;'>{$_SESSION['registration_message']}</p>";
            unset($_SESSION['registration_message']);
        }
        ?>

        <h2>Employee Search</h2>
        <form action="register.php" method="POST" class="search-form">
            <select name="search_type" required>
                <option value="name" <?php echo ($search_type == "name") ? 'selected' : ''; ?>>Search by Name</option>
                <option value="role" <?php echo ($search_type == "role") ? 'selected' : ''; ?>>Search by Role</option>
            </select>
            <input type="text" name="search_value" placeholder="Enter search term" value="<?php echo htmlspecialchars($search_value); ?>" required>
            <button type="submit" name="search">Search</button>
        </form>

        <h3>Employee List</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Salary</th>
                    <th>Hire Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['role']}</td>
                            <td>{$row['salary']}</td>
                            <td>{$row['hire_date']}</td>
                            <td>
                                <a href='register.php?edit_id={$row['id']}' class='edit-btn'>Edit</a>
                                <a href='register.php?delete_id={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this employee?\");'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No employees found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if (isset($employee_data)): ?>
            <h3>Edit Employee</h3>
            <form action="register.php" method="POST">
                <input type="hidden" name="employee_id" value="<?php echo $employee_data['id']; ?>">
                <input type="text" name="employee_name" placeholder="Employee Name" value="<?php echo htmlspecialchars($employee_data['name']); ?>" required>
                <input type="text" name="employee_role" placeholder="Role" value="<?php echo htmlspecialchars($employee_data['role']); ?>" required>
                <input type="number" step="0.01" name="employee_salary" placeholder="Salary" value="<?php echo htmlspecialchars($employee_data['salary']); ?>" required>
                <input type="date" name="hire_date" value="<?php echo $employee_data['hire_date']; ?>" required>
                <button type="submit" name="update_employee">Update</button>
            </form>
        <?php endif; ?>

        <!-- Button to go to Dashboard -->
        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="action-btn">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>
