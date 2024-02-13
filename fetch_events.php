<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "calendar_online";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];

$day = $_POST['day'];
$month = $_POST['month'];
$year = $_POST['year'];

// Use prepared statement to prevent SQL injection
$query = "SELECT title, description, hour FROM events WHERE email = ? AND day = ? AND month = ? AND year = ?";
$stmt = mysqli_prepare($conn, $query);

// Bind parameters
mysqli_stmt_bind_param($stmt, "siii", $email, $day, $month, $year);

// Execute statement
mysqli_stmt_execute($stmt);

// Bind result variables
mysqli_stmt_bind_result($stmt, $title, $description, $hour);

$events = [];

// Fetch values
while (mysqli_stmt_fetch($stmt)) {
    $events[] = ['title' => $title, 'description' => $description, 'hour' => $hour];
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode($events);
?>
