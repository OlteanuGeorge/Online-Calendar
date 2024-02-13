<?php
$first_name = $_POST['first-name'];
$last_name = $_POST['last-name'];
$email = $_POST['email'];
$password = $_POST['password'];
$hash = password_hash($password, PASSWORD_DEFAULT);

if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($password)) {
    $host = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "calendar_online";

    // Create connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
    } else {
        // Use prepared statements to prevent SQL injection
        $SELECT = "SELECT email FROM register WHERE email = ? LIMIT 1";
        $INSERT = "INSERT INTO register (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        // $CREATE_TABLE = "CREATE TABLE IF NOT EXISTS `" . $email . "` (
        //             id INT PRIMARY KEY AUTO_INCREMENT,
        //             title VARCHAR(50) NOT NULL,
        //             description VARCHAR(50) NOT NULL,
        //             hour TIME
        //         )";

        // Check if the email already exists
        $stmt = $conn->prepare($SELECT);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $rnum = $stmt->num_rows;
        $stmt->close();

        if ($rnum == 0) {
            // If the email is not registered, proceed with registration
            $stmt = $conn->prepare($INSERT);
            $stmt->bind_param("ssss", $first_name, $last_name, $email, $hash);
            $stmt->execute();
            $stmt->close();

            // Create a table for the user if not exists
            // $conn->query($CREATE_TABLE);

            // Redirect to login page
            header("Location: login.html");
        } else {
            // Email already exists
            //echo '<script>alert("Email already registered.")</script>';
            // Redirect to register page
            header("Location: register_failed.html");
        }

        $conn->close();
    }
} else {
    // All fields are required
    echo "All fields are required!";
    die();
}
?>
