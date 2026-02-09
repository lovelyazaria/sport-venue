<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$sport = mysqli_real_escape_string($conn, $_GET['sport']);

// Mapping nama sport ke sport_id
$sport_mapping = [
    'Football' => 1,
    'Basketball' => 2,
    'Tennis' => 3,
    'Volleyball' => 4,
    'Badminton' => 5,
    'Swimming' => 6
];

$sport_id = isset($sport_mapping[$sport]) ? $sport_mapping[$sport] : 0;

$sql = "SELECT id as field_id, field_name, harga as price_per_hour 
        FROM fields 
        WHERE sport_id = $sport_id AND is_available = 1
        ORDER BY field_name";

$result = $conn->query($sql);

$fields = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fields[] = $row;
    }
}

echo json_encode($fields);
$conn->close();
?>