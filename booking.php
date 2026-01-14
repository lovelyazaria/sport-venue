<?php
require_once 'config.php';

// ==========================
// ONLY ALLOW POST
// ==========================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

// ==========================
// GET INPUT (JANGAN CAST DULU)
// ==========================
$name        = sanitizeInput($_POST['name'] ?? '');
$email       = sanitizeInput($_POST['email'] ?? '');
$sportRaw    = $_POST['sport'] ?? '';
$date        = sanitizeInput($_POST['date'] ?? '');
$time        = sanitizeInput($_POST['time'] ?? '');
$durationRaw = $_POST['duration'] ?? '';

// ==========================
// VALIDATION
// ==========================
if (
    $name === '' ||
    $email === '' ||
    $sportRaw === '' ||
    $date === '' ||
    $time === '' ||
    $durationRaw === ''
) {
    die("<h2>Error: Semua field wajib diisi.</h2>");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("<h2>Error: Format email tidak valid.</h2>");
}

if ($date < date('Y-m-d')) {
    die("<h2>Error: Tanggal booking tidak boleh di masa lalu.</h2>");
}

// ==========================
// CAST SETELAH VALID
// ==========================
$sportId  = (int) $sportRaw;
$duration = (int) $durationRaw;

// ==========================
// CHECK SLOT AVAILABILITY
// ==========================
if (!isSlotAvailable($sportId, $date, $time, $duration)) {
    die("<h2>Slot tidak tersedia. Silakan pilih waktu lain.</h2>");
}

// ==========================
// INSERT BOOKING
// ==========================
$conn = getDBConnection();

$stmt = $conn->prepare("
    INSERT INTO bookings 
    (user_name, user_email, sport_id, booking_date, booking_time, duration_hours, status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending')
");

$stmt->bind_param(
    "ssissi",
    $name,
    $email,
    $sportId,
    $date,
    $time,
    $duration
);

if (!$stmt->execute()) {
    die("<h2>Gagal menyimpan booking: {$stmt->error}</h2>");
}

$bookingId = $conn->insert_id;
$sportName = getSportName($sportId);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Berhasil</title>
</head>
<body>

<h2>Booking Berhasil 🎉</h2>

<p><strong>Nama:</strong> <?= $name ?></p>
<p><strong>Olahraga:</strong> <?= $sportName ?></p>
<p><strong>Tanggal:</strong> <?= $date ?></p>
<p><strong>Jam:</strong> <?= $time ?></p>
<p><strong>Durasi:</strong> <?= $duration ?> jam</p>
<p><strong>ID Booking:</strong> <?= $bookingId ?></p>

<a href="index.html">Kembali ke Home</a>

</body>
</html>
