<?php
session_start();

require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);

$mail->isSMTP();

$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = "topdroid9822@gmail.com";
$mail->Password = "fohy vrnf avog pkaa ";
$mail->IsHTML(true);
function extractEmails($inputString) {
    $emailPattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Z|a-z]{2,}\b/';

    // Use preg_match_all to extract all email addresses from the input string
    preg_match_all($emailPattern, $inputString, $matches);

    // $matches[0] contains an array of matched email addresses
    return $matches[0];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["meettitle"];
    $description = $_POST["meetdescription"];
    $starting_hour = $_POST["starting_hour"];
    $finishing_hour = $_POST["finishing_hour"];
    $day = $_POST["meetdate"];
    $participants = $_POST["participants"];


    $month = $_POST["meetmonth"];
    $year = $_POST["meetyear"];
    
    // Validate the data (add more validation if needed)
    if (!empty($title) && !empty($description) && !empty($starting_hour) && !empty($finishing_hour) && !empty($participants) && !empty($day) && !empty($month) && !empty($year)) {
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
                
                $INSERT = "INSERT INTO meets (email, title, description, starting_hour, finishing_hour, day, month, year, participants) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($INSERT);
                $stmt->bind_param("sssssssss", $email, $title, $description, $starting_hour, $finishing_hour, $day, $month, $year, $participants);
                
                

                if ($stmt->execute()) {
                    // Event saved successfully
                    $subject = "You have a new meeting: $title";
                    $message = "$email invited you to a meeting on $day/$month/$year, starting at $starting_hour and finishing at $finishing_hour. Other participants: $participants. You can see more details about the meeting in your <a href='localhost/calendar/home'>calendar</a>. If you can't attend please contact the host.";
                   $inputText = $participants;
                   $participants .=" Host: $email"; 
                    $extractedEmails = extractEmails($inputText);
                    $mail->Subject = $subject;
                        $mail->Body = $message;
                    //echo "Extracted Email Addresses:\n";
                    foreach ($extractedEmails as $email_participant) {
                    //echo $email . "\n";
                        $mail->setFrom("topdroid9822@gmail.com");
                        $mail->addAddress($email_participant);
                                
                        $INSERT2 = "INSERT INTO meets (email, title, description, starting_hour, finishing_hour, day, month, year, participants) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($INSERT2);
                $stmt->bind_param("sssssssss", $email_participant, $title, $description, $starting_hour, $finishing_hour, $day, $month, $year, $participants);
                        
                            if($stmt->execute()){
                                
                                header("Location: calendar.php");
                            }
                        }
                          $mail->send();       
                   
                } else {
                    // Error saving event
                    echo "Error saving meet: " . $stmt->error;
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
