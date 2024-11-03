<?php
// config/db.php

$host = 'localhost';                 // Database host
$dbname = 'restaurant_management';   // Database name
$username = 'root';                  // Database username
$password = '';                      // Database password (default is empty for XAMPP)

$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}