<?php
session_start();
require_once 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// 2. Ambil data terbaru dari database
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

// 3. Logika Update Profil & Password
if (isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $new_pass = $_POST['new_password'];
    
    // Penanganan Upload Foto
    $nama_file = $_FILES['new_profile_picture']['name'];
    $tmp_file  = $_FILES['new_profile_picture']['tmp_name'];
    $foto_final = $user['profile_picture']; 

    if (!empty($nama_file)) {
        // Hapus foto lama jika ada
        if (!empty($user['profile_picture']) && file_exists("uploads/" . $user['profile_picture'])) {
            unlink("uploads/" . $user['profile_picture']);
        }
        $foto_final = time() . "_" . $nama_file;
        move_uploaded_file($tmp_file, "uploads/" . $foto_final);
    }

    // Update Data Dasar
    $sql_update = "UPDATE users SET 
                    full_name = '$fullname', 
                    address = '$address',
                    whatsapp = '$whatsapp',
                    profile_picture = '$foto_final' 
                    WHERE id = '$user_id'";
    
    $update = mysqli_query($conn, $sql_update);

    // Update Password jika diisi
    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed_pass' WHERE id = '$user_id'");
    }

    if ($update) {
        $_SESSION['user']['full_name'] = $fullname; // Update nama di session
        $_SESSION['toast_msg'] = "Profil berhasil diperbarui!";
        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .logo-heavy-shadow { filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2)); }
    </style>
</head>
<body class="text-slate-900">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3">
                <img src="logo.png" alt="Logo" class="w-10 h-10 object-contain logo-heavy-shadow">
                <span class="text-xl font-black tracking-tighter text-slate-900 uppercase">WANT<span class="text-blue-600">2</span>BUY</span>
            </a>
            <a href="dashboard.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition">Kembali ke Dashboard</a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12">
                
                <div class="md:col-span-4 bg-slate-900 p-10 text-white flex flex-col items-center text-center">
                    <div class="relative group mb-6">
                        <?php 
                            $img_path = (!empty($user['profile_picture']) && file_exists("uploads/" . $user['profile_picture'])) 
                                        ? "uploads/" . $user['profile_picture'] : "";
                        ?>
                        <img id="preview" src="<?= $img_path ?>" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=0284c7&color=fff&size=200'" 
                             class="w-40 h-40 rounded-[2.5rem] object-cover border-4 border-slate-800 shadow-2xl transition-transform group-hover:scale-105">
                    </div>
                    <h2 class="text-xl font-black mb-1"><?= htmlspecialchars($user['full_name']) ?></h2>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-8">@<?= htmlspecialchars($user['username']) ?></p>
                    
                    <div class="w-full space-y-4 pt-8 border-t border-slate-800 text-left">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-500">
                            <span>Email</span>
                            <span class="text-slate-300"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-8 p-10 md:p-14">
                    <form method="POST" enctype="multipart/form-data" class="space-y-8">
                        
                        <div class="space-y-6">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-blue-600">Informasi Kontak</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">WhatsApp</label>
                                    <input type="text" name="whatsapp" value="<?= htmlspecialchars($user['whatsapp'] ?? '') ?>" placeholder="08..." class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Utama (COD)</label>
                                <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Tulis alamat lengkap/kota..." class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-50 space-y-6">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-red-500">Privasi & Foto</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password Baru</label>
                                    <input type="password" name="new_password" placeholder="Kosongkan jika tak diubah" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-red-500 outline-none transition">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Upload Foto Baru</label>
                                    <input type="file" name="new_profile_picture" accept="image/*" class="w-full text-xs font-bold text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition" onchange="previewImage(event)">
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="update_profile" class="w-full py-5 bg-slate-900 hover:bg-blue-600 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl active:scale-[0.98]">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php if (isset($_SESSION['toast_msg'])): ?>
    <div id="toast" class="fixed bottom-10 right-10 z-[100] transform transition-all duration-500 translate-y-20 opacity-0">
        <div class="bg-slate-900 text-white px-6 py-4 rounded-3xl shadow-2xl flex items-center gap-4 border border-slate-800">
            <div class="bg-blue-600 p-2 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest"><?= $_SESSION['toast_msg']; ?></p>
        </div>
    </div>
    <script>
        const toast = document.getElementById('toast');
        setTimeout(() => { toast.classList.remove('translate-y-20', 'opacity-0'); toast.classList.add('translate-y-0', 'opacity-100'); }, 100);
        setTimeout(() => { toast.classList.add('opacity-0', 'translate-y-10'); setTimeout(() => toast.remove(), 500); }, 4000);
    </script>
    <?php unset($_SESSION['toast_msg']); endif; ?>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){ document.getElementById('preview').src = reader.result; }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>