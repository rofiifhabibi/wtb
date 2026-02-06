<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $fullname = $_POST['full_name'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $age      = $_POST['age'];

    // Karena upload dibatalkan, foto profil otomatis diset ke default.png
    $foto_default = "default.png";

    $sql = "INSERT INTO users (full_name, username, email, password, age, profile_picture) 
            VALUES ('$fullname', '$username', '$email', '$password', '$age', '$foto_default')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Berhasil daftar! Silahkan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | Lapak Kita</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        button { width: 100%; padding: 14px; background: #4e73df; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }
        button:hover { background: #2e59d9; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
        a { color: #4e73df; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Daftar Akun</h2>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Nama Lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email (Gmail)" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="number" name="age" placeholder="Umur" required>
            <button type="submit" name="register">BUAT AKUN</button>
        </form>
        <div class="footer">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>