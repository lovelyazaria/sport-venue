<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['users_id'])) {
    header("Location: profile.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['photo'])) {
    $user_id = $_SESSION['users_id'];
    $file = $_FILES['photo'];
    
    // Validasi file
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed)) {
        $_SESSION['error'] = "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.";
        header("Location: profile.php");
        exit;
    }
    
    // Cek ukuran file (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 5MB.";
        header("Location: profile.php");
        exit;
    }
    
    // Buat nama file unik
    $new_filename = 'uploads/' . uniqid() . '_' . $filename;
    
    // Buat folder uploads kalau belum ada
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $new_filename)) {
        // Hapus foto lama kalau bukan default
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $old_data = $result->fetch_assoc();
        
        if ($old_data['photo'] && $old_data['photo'] != 'profilee.jpeg' && file_exists($old_data['photo'])) {
            unlink($old_data['photo']);
        }
        $stmt->close();
        
        // Update database
        $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $stmt->bind_param("si", $new_filename, $user_id);
        
        if ($stmt->execute()) {
            // Update session
            $_SESSION['photo'] = $new_filename;
            $_SESSION['success'] = "Foto berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Gagal menyimpan foto ke database.";
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error'] = "Gagal mengupload file.";
    }
}

header("Location: profile.php");
exit;
?>