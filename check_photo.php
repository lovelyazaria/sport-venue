<?php
session_start();

echo "<h2>Debug Photo</h2>";
echo "Session photo: " . ($_SESSION['photo'] ?? 'TIDAK ADA') . "<br>";
echo "File exists: " . (file_exists($_SESSION['photo'] ?? '') ? 'YES' : 'NO') . "<br>";
echo "<br>";

require_once 'config.php';
$conn = getDBConnection();

if (isset($_SESSION['users_id'])) {
    $stmt = $conn->prepare("SELECT id, name, email, photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['users_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    echo "<h3>Data dari Database:</h3>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    if ($user['photo']) {
        echo "Photo path: " . $user['photo'] . "<br>";
        echo "File exists: " . (file_exists($user['photo']) ? 'YES' : 'NO') . "<br>";
        echo "<img src='" . $user['photo'] . "' width='200'>";
    }
} else {
    echo "Belum login!";
}
?>