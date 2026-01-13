<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change this to your MySQL username
define('DB_PASS', ''); // Change this to your MySQL password
define('DB_NAME', 'sports_booking');

// Create database connection
function getDBConnection() {
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            $conn->set_charset("utf8");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    return $conn;
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to get sport name by ID
function getSportName($sportId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT name FROM sports WHERE id = ?");
    $stmt->bind_param("i", $sportId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sport = $result->fetch_assoc();
    $stmt->close();
    return $sport ? $sport['name'] : 'Unknown Sport';
}

// Function to check if booking slot is available
function isSlotAvailable($sportId, $date, $time, $duration) {
    $conn = getDBConnection();

    // Calculate end time
    $startTime = strtotime($time);
    $endTime = strtotime("+$duration hours", $startTime);
    $endTimeStr = date('H:i:s', $endTime);

    // Check for overlapping bookings
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings
        WHERE sport_id = ?
        AND booking_date = ?
        AND status != 'cancelled'
        AND (
            (booking_time <= ? AND DATE_ADD(booking_time, INTERVAL duration_hours HOUR) > ?) OR
            (booking_time < ? AND DATE_ADD(booking_time, INTERVAL duration_hours HOUR) >= ?) OR
            (? <= booking_time AND ? > booking_time)
        )
    ");

    $stmt->bind_param("issssss", $sportId, $date, $time, $time, $endTimeStr, $endTimeStr, $time, $endTimeStr);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();

    return $count == 0;
}
?>
