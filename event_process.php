<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $hour = $_POST["hour"];
    $day = $_POST["date"];

    $month = $_POST["month"];
    $year = $_POST["year"];
    
    // Validate the data (add more validation if needed)
    if (!empty($title) && !empty($description) && !empty($hour) && !empty($day) && !empty($month) && !empty($year)) {
        // Assuming you have a database connection already established
        $host = "localhost";
        $dbUsername = "root";
        $dbPassword = "";
        $dbname = "calendar_online";

        $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
        if (mysqli_connect_error()) {
            die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
        } else {
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
    
                $INSERT = "INSERT INTO events (email, title, description, hour, day, month, year) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($INSERT);
                $stmt->bind_param("sssssss", $email, $title, $description, $hour, $day, $month, $year);
                
                if ($stmt->execute()) {
                    // Event saved successfully
                    echo "Event saved successfully!";
                    header("Location: calendar.php");
                } else {
                    // Error saving event
                    echo "Error saving event: " . $stmt->error;
                }

                $stmt->close();
                $conn->close();
            }
        }
    } else {
        // Handle validation errors
        header("Location: calendar.php");
    }
} else {
    // Handle non-POST requests
    echo "Invalid request method!";
}
?>
