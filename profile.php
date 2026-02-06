<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
$user = mysqli_fetch_assoc($query);

// Logika Update Profil
if (isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
    // Umur dan Email tidak diambil dari $_POST karena tidak boleh diubah
    
    // Penanganan Upload Foto
    $nama_file = $_FILES['new_profile_picture']['name'];
    $tmp_file  = $_FILES['new_profile_picture']['tmp_name'];

    if ($nama_file != "") {
        if ($user['profile_picture'] != "default.png") {
            unlink("uploads/" . $user['profile_picture']);
        }
        $foto_final = time() . "_" . $nama_file;
        move_uploaded_file($tmp_file, "uploads/" . $foto_final);
    } else {
        $foto_final = $user['profile_picture'];
    }

    // Query UPDATE (Hanya Nama Lengkap dan Foto Profil)
    $update = mysqli_query($conn, "UPDATE users SET 
                full_name = '$fullname', 
                profile_picture = '$foto_final' 
                WHERE username = '$username'");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil | Lapak Kita</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fc; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #333; height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; border-bottom: 1px solid #555; padding-bottom: 20px; }
        .sidebar a { display: block; color: #ccc; padding: 12px; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a:hover { background: #444; color: white; }
        
        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        .profile-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: auto; }
        
        .profile-header { text-align: center; margin-bottom: 30px; }
        .current-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #4e73df; margin-bottom: 10px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: bold; color: #555; margin-bottom: 8px; }
        
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px; }
        
        /* Gaya untuk input yang tidak bisa diubah */
        .readonly-input { background-color: #f1f3f5; color: #777; cursor: not-allowed; border: 1px solid #e9ecef; }
        
        .btn-save { width: 100%; background: #4e73df; color: white; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }
        .btn-save:hover { background: #2e59d9; }
        .info-tag { font-size: 12px; color: #a1a1a1; font-style: italic; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Lapak Kita</h2>
        <a href="dashboard.php">?? Dashboard</a>
        <a href="profile.php" style="background: #4e73df; color: white;">?? Profil Saya</a>
        <a href="logout.php" style="color: #ff7675;">?? Keluar</a>
    </div>

    <div class="main-content">
        <div class="profile-card">
            <div class="profile-header">
                <img src="uploads/<?php echo $user['profile_picture']; ?>" class="current-img">
                <h2 style="margin: 0;">Pengaturan Profil</h2>
                <p style="color: #888;">Kelola informasi akun Anda</p>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Ganti Foto Profil</label>
                    <input type="file" name="new_profile_picture" accept="image/*">
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

                <div class="form-group">
                    <label>Gmail (Akun Utama)</label>
                    <input type="email" value="<?php echo $user['email']; ?>" class="readonly-input" readonly>
                    <div class="info-tag">* Gmail tidak dapat diubah untuk keamanan akun.</div>
                </div>

                <div class="form-group">
                    <label>Umur</label>
                    <input type="number" value="<?php echo $user['age']; ?>" class="readonly-input" readonly>
                    <div class="info-tag">* Data umur bersifat permanen.</div>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo $user['username']; ?>" class="readonly-input" readonly>
                </div>
                
                <button type="submit" name="update_profile" class="btn-save">SIMPAN PERUBAHAN</button>
            </form>
        </div>
    </div>

</body>
</html>