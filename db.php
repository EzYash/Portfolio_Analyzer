<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "portfolio_analyzer";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure proper UTF‑8 encoding for text data
$conn->set_charset("utf8mb4");

?>