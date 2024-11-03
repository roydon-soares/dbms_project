<?php
// Remove session_start(); as it's already started in login.php

function login($username, $password) {
    // Predefined credentials (replace with database validation if needed)
    $valid_username = 'admin';
    $valid_password = 'password123';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header("Location: ../pages/dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid Username or Password!";
        header("Location: ../pages/login.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    login($username, $password);
}