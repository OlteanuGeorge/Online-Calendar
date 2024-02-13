<?php
session_start();

$email = $_POST['email'];
$password = $_POST['password'];

if (!empty($email) && !empty($password)) {
    $host = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "calendar_online";

    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
    if(mysqli_connect_error()) {
        die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
    } else {

        // Use a prepared statement to prevent SQL injection
        $SELECT = "SELECT password, first_name, last_name, email FROM register WHERE email = ?";
        $stmt = $conn->prepare($SELECT);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Fetch the hashed password, first_name, last_name, and email from the result
            $stmt->bind_result($hashed_password, $first_name, $last_name, $fetched_email);
            $stmt->fetch();

            // Verify the entered password against the hashed password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['email'] = $fetched_email;

                header("Location: calendar.php");
                exit();
            } else {
                header("Location: login_failed.html"); // Incorrect password
            }
        } else {
            header("Location: login_failed.html"); // User not found
        }

        $stmt->close();
        $conn->close();
    }
} else {
    echo "Email și parolă sunt necesare!";
}
?>
