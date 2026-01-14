<?php
// ==========================
// ERROR REPORTING (WAJIB)
// ==========================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================
// DATABASE CONFIG
// ==========================
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sports_booking');

// ==========================
// DATABASE CONNECTION
// ==========================
function getDBConnection() {
    static $conn = null;

    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $conn->set_charset("utf8mb4");
    }

    return $conn;
}

// ==========================
// SANITIZE INPUT
// ==========================
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ==========================
// GET SPORT NAME
// ==========================
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

// ==========================
// CHECK SLOT AVAILABILITY
// ==========================
function isSlotAvailable($sportId, $date, $time, $duration) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM bookings
        WHERE sport_id = ?
        AND booking_date = ?
        AND status != 'cancelled'
        AND (
            booking_time < ADDTIME(?, SEC_TO_TIME(? * 3600))
            AND ADDTIME(booking_time, SEC_TO_TIME(duration_hours * 3600)) > ?
        )
    ");

    $stmt->bind_param("isiss", $sportId, $date, $time, $duration, $time);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total'];
    $stmt->close();

    return $count == 0;
}
