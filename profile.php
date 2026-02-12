<?php
session_start();
require_once 'config.php';

// ==================== PROSES REGISTER ====================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $name  = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $conn = getDBConnection();

        // Cek email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                // LANGSUNG LOGIN SETELAH REGISTER
                $_SESSION['users_id'] = $conn->insert_id; // ID user yang baru dibuat
                $_SESSION['name']     = $name;
                $_SESSION['email']    = $email;
                $_SESSION['photo']    = 'profilee.jpeg'; // Default photo
                $_SESSION['success']  = "Welcome! Your account has been created successfully.";
                
                header("Location: profile.php");
                exit;
            } else {
                $error = "Registration failed";
            }
        }

        $stmt->close();
        $conn->close();
    }
}

// ==================== PROSES LOGIN ====================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $conn = getDBConnection();
    
    $stmt = $conn->prepare(
        "SELECT id, name, email, password, photo FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['users_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['photo']   = $user['photo'] ?? 'profilee.jpeg';

        header("Location: profile.php");
        exit;
    } else {
        $error_login = "Invalid email or password";
    }

    $stmt->close();
    $conn->close();
}

// Ambil data user kalau sudah login
$name  = $_SESSION['name']  ?? '';
$email = $_SESSION['email'] ?? '';
$photo = $_SESSION['photo'] ?? 'profilee.jpeg';
$isLoggedIn = isset($_SESSION['users_id']);

// Tentukan tampilan apa yang ditampilkan
$showRegister = isset($_GET['register']);
$showLogin = isset($_GET['login']) || !$isLoggedIn;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isLoggedIn ? 'My Profile' : ($showRegister ? 'Register' : 'Login') ?> - Arena Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header / Navbar -->
    <header>
        <nav>
            <div class="logo">
                <img src="pict_court/logo.jpeg" alt="Logo" class="logo-img">
                <h1>Arena Book</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="index.html#sports">Sports</a></li>
                <li><a href="booking.html">Booking</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="profile.php">Profile</a></li>
                <?php else: ?>
                    <li><a href="profile.php?login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <?php if ($isLoggedIn): ?>
        <!-- ==================== TAMPILAN PROFILE ==================== -->
        <div class="profile-header">
            <div class="profile-photo">
                <img src="<?= htmlspecialchars($photo) ?>" alt="Profile Photo">
            </div>

            <h2 class="profile-name"><?= htmlspecialchars($name) ?></h2>

            <form action="uploadpt.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="photo" id="photo" accept="image/*" hidden required>
                <label for="photo" class="btn-upload">Pilih Foto</label>
                <button type="submit" class="btn-save">Simpan Foto</button>
            </form>
        </div>

        <section class="profile-section">
            <div class="profile-card">
                <h2>My Profile</h2>

                <div class="profile-item">
                    <label>Full Name</label>
                    <p><?= htmlspecialchars($name) ?></p>
                </div>

                <div class="profile-item">
                    <label>Email</label>
                    <p><?= htmlspecialchars($email) ?></p>
                </div>

                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </section>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin: 1rem auto; max-width: 500px; text-align: center;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin: 1rem auto; max-width: 500px; text-align: center;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

    <?php elseif ($showRegister): ?>
        <!-- ==================== TAMPILAN REGISTER ==================== -->
        <section class="login-section">
            <div class="login-container">
                <div class="login-form">
                    <h2>Create Account</h2>
                    <p>Join us to start booking sports fields</p>
                    
                    <?php if (isset($error)): ?>
                        <div style="background: #fee; color: #c33; padding: 10px; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="profile.php?register" method="POST">
                        <input type="hidden" name="register" value="1">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn">Create Account</button>
                    </form>
                    <div class="login-links">
                        <a href="profile.php?login">Already have an account? Sign In</a>
                    </div>
                </div>
            </div>
        </section>

    <?php else: ?>
        <!-- ==================== TAMPILAN LOGIN ==================== -->
        <section class="login-section">
            <div class="login-container">
                <div class="login-form">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account</p>
                    
                    <?php if (isset($_SESSION['success_register'])): ?>
                        <div style="background: #efe; color: #363; padding: 10px; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                            <?php echo htmlspecialchars($_SESSION['success_register']); unset($_SESSION['success_register']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_login)): ?>
                        <div style="background: #fee; color: #c33; padding: 10px; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                            <?php echo htmlspecialchars($error_login); ?>
                        </div>
                    <?php endif; ?>

                    <form action="profile.php?login" method="POST">
                        <input type="hidden" name="login" value="1">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn">Sign In</button>
                    </form>
                    <div class="login-links">
                        <a href="#">Forgot Password?</a>
                        <a href="profile.php?register">Create Account</a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2026 Arena. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>