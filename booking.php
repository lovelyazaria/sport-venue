<?php
// 1. Koneksi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 2. Proses Form saat tombol submit ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sport = mysqli_real_escape_string($conn, $_POST['sport']);
    $field_id = (int)$_POST['field_id']; 
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $duration = (int)$_POST['duration'];

    // 3. Ambil harga dari database berdasarkan field_id
    $price_query = "SELECT harga as price_per_hour, field_name FROM fields WHERE id = $field_id";
    $price_result = $conn->query($price_query);
    
    if ($price_result->num_rows > 0) {
        $field_data = $price_result->fetch_assoc();
        $price_per_hour = $field_data['price_per_hour'];
        $field_name = $field_data['field_name'];
        
        // Hitung Total
        $total_price = $duration * $price_per_hour;
    } else {
        die("Lapangan tidak ditemukan!");
    }

    // 4. Simpan ke Database
    $sql = "INSERT INTO bookings (user_name, user_email, sport_type, field_id, booking_date, booking_time, duration_hours, status) 
            VALUES ('$name', '$email', '$sport', '$field_id', '$date', '$time', '$duration', 'pending')";

    if ($conn->query($sql) === TRUE) {
        $bookingId = $conn->insert_id;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Confirmation</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
        
        <div style="background: linear-gradient(135deg, #0a1f44, #1e40af); padding: 50px 0; display: flex; justify-content: center; font-family: sans-serif; min-height: 100vh;">
            <div style="background: white; width: 450px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); height: fit-content;">
                <div style="background: linear-gradient(135deg, #0a1f44, #1e40af); color: white; padding: 40px 20px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: white; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 30px;">✓</div>
                    <h2 style="margin: 0;">Booking Berhasil!</h2>
                    <p style="margin: 10px 0 0; opacity: 0.9;">ID Booking: #<?php echo str_pad($bookingId, 5, '0', STR_PAD_LEFT); ?></p>
                </div>
                
                <div style="padding: 20px 40px;">
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Nama</span> <strong><?php echo $name; ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Olahraga</span> <strong><?php echo $sport; ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Lapangan</span> <strong><?php echo $field_name; ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Tanggal</span> <strong><?php echo date('d M Y', strtotime($date)); ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Waktu</span> <strong><?php echo $time; ?> WIB</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Durasi</span> <strong><?php echo $duration; ?> Jam</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #eee;">
                        <span>Harga/Jam</span> <strong>Rp <?php echo number_format($price_per_hour, 0, ',', '.'); ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0;">
                        <span style="font-size: 18px;">Total Bayar</span> <strong style="color: #f97316; font-size: 20px;">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></strong>
                    </div>
                </div>

                <div style="padding: 0 40px 40px;">
                    <button onclick="window.print()" style="width:100%; background:#3b82f6; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; cursor:pointer; margin-bottom:10px;">💾 Simpan Bukti</button>
                    <a href="index.html" style="display:block; text-align:center; background:#e5e7eb; color:#4b5563; padding:12px; border-radius:8px; text-decoration:none; font-weight:bold;">Kembali ke Home</a>
                </div>
            </div>
        </div>

        </body>
        </html>
        <?php
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>