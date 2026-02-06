<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($query) === 1) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['login'] = true;
        $_SESSION['username'] = $data['username'];
        $_SESSION['full_name'] = $data['full_name'];
        $_SESSION['profile_picture'] = $data['profile_picture'];
        
        echo "<script>alert('Login Berhasil!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Username atau Password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Lapak Kita</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; border: none; color: white; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 10px; }
        button:hover { background: #218838; }
        p { text-align: center; margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Login Lapak Kita</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">MASUK</button>
        </form>
        <p>Belum punya akun? <a href="register.php" style="color:#28a745;">Daftar</a></p>
    </div>
</body>
</html>