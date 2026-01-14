<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan Bersihkan Input
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $sportId = (int)$_POST['sport']; 
    $field_id = isset($_POST['field_id']) ? (int)$_POST['field_id'] : 0;
    $date = sanitizeInput($_POST['date']);
    $time = sanitizeInput($_POST['time']);
    $duration = (int)$_POST['duration'];

    // 2. Validasi Dasar
    if (empty($name) || empty($email) || $field_id == 0) {
        die("Data tidak lengkap. Pastikan Lapangan sudah dipilih.");
    }

    $conn = getDBConnection();

    // 3. Perbaikan Query (Gunakan bind_param untuk MySQLi)
    $stmt = $conn->prepare("INSERT INTO bookings (user_name, user_email, sport_id, field_id, booking_date, booking_time, duration_hours, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    
    // "ssiissi" = string, string, int, int, string, string, int
    $stmt->bind_param("ssiissi", $name, $email, $sportId, $field_id, $date, $time, $duration);

    if ($stmt->execute()) {
        $bookingId = $conn->insert_id; // Ambil ID yang baru masuk
        $sportName = getSportName($sportId); // Ambil nama olahraga dari database

        // --- 4. TAMPILAN UI TIKET (Gantikan submit.html) ---
        include 'header.php'; 
        ?>
        <div style="background: #f4f7f6; padding: 50px 0; display: flex; justify-content: center; font-family: 'Segoe UI', sans-serif;">
            <div style="background: white; width: 400px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div style="background: #2ecc71; color: white; padding: 30px; text-align: center;">
                    <div style="width: 50px; height: 50px; background: white; color: #2ecc71; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-weight: bold; font-size: 24px;">✓</div>
                    <h2 style="margin: 0;">Booking Berhasil! 🎉</h2>
                    <p style="opacity: 0.9; margin: 5px 0;">ID Booking: #<?php echo str_pad($bookingId, 3, '0', STR_PAD_LEFT); ?></p>
                </div>
                
                <div style="padding: 20px 30px;">
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;">
                        <span style="color: #888;">Nama Pelanggan</span> <strong><?php echo $name; ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;">
                        <span style="color: #888;">Cabang Olahraga</span> <strong><?php echo $sportName; ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;">
                        <span style="color: #888;">Tanggal</span> <strong><?php echo $date; ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;">
                        <span style="color: #888;">Jam Mulai</span> <strong><?php echo $time; ?> WIB</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0;">
                        <span style="color: #888;">Durasi</span> <strong><?php echo $duration; ?> Jam</strong>
                    </div>
                </div>

                <div style="padding: 0 30px 30px;">
                    <button onclick="window.print()" style="width: 100%; background: #3498db; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-bottom: 10px;">⬇ Simpan Bukti</button>
                    <a href="index.php" style="display: block; text-align: center; background: #eee; color: #333; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">Kembali ke Home</a>
                </div>
            </div>
        </div>
        <?php
        include 'footer.php';
        exit;
    } else {
        echo "Gagal menyimpan: " . $stmt->error;
    }
    $stmt->close();
}
?>