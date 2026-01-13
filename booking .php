<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $sport = sanitizeInput($_POST['sport']);
    $date = sanitizeInput($_POST['date']);
    $time = sanitizeInput($_POST['time']);
    $duration = (int)sanitizeInput($_POST['duration']);

    // Basic validation
    if (empty($name) || empty($email) || empty($sport) || empty($date) || empty($time) || empty($duration)) {
        echo "<h2>Error: All fields are required.</h2>";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<h2>Error: Invalid email format.</h2>";
        exit;
    }

    // Validate date (must be today or future)
    $today = date('Y-m-d');
    if ($date < $today) {
        echo "<h2>Error: Booking date cannot be in the past.</h2>";
        exit;
    }

    // Check if slot is available
    if (!isSlotAvailable($sport, $date, $time, $duration)) {
        echo "<h2>Sorry, this time slot is not available.</h2>";
        echo "<p>Please choose a different time or date.</p>";
        echo "<p><a href='index.html#booking'>Back to Booking Form</a></p>";
        exit;
    }

    // Insert booking into database
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO bookings (user_name, user_email, sport_id, booking_date, booking_time, duration_hours, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");

    if ($stmt) {
        $stmt->bind_param("sssssi", $name, $email, $sport, $date, $time, $duration);

        if ($stmt->execute()) {
            $bookingId = $conn->insert_id;
            $sportName = getSportName($sport);

            // Send confirmation email
            $to = $email;
            $subject = "Booking Confirmation - Sports Field Booking";
            $message = "
            Dear $name,

            Thank you for your booking request!

            Booking Details:
            - Sport: $sportName
            - Date: $date
            - Time: $time
            - Duration: $duration hours
            - Status: Pending Confirmation

            Booking ID: $bookingId

            We will review your booking and send you a confirmation email within 24 hours.

            If you have any questions, please contact us.

            Best regards,
            Sports Field Booking Team
            ";

            $headers = "From: noreply@sportsfieldbooking.com\r\n";
            $headers .= "Reply-To: admin@sportsfieldbooking.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            mail($to, $subject, $message, $headers);

            echo "<h2>Thank you for your booking request!</h2>";
            echo "<p>We have received your booking for $sportName on $date at $time for $duration hours.</p>";
            echo "<p>Booking ID: <strong>$bookingId</strong></p>";
            echo "<p>You will receive a confirmation email shortly.</p>";
            echo "<p><a href='index.html'>Back to Home</a></p>";
        } else {
            echo "<h2>Sorry, there was an error processing your booking.</h2>";
            echo "<p>Please try again later or contact us directly.</p>";
        }

        $stmt->close();
    } else {
        echo "<h2>Sorry, there was an error processing your booking.</h2>";
        echo "<p>Please try again later or contact us directly.</p>";
    }

    $conn->close();
} else {
    // Redirect to home if accessed directly
    header("Location: index.html");
    exit;
}
?>
