<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['user'])) { 
    header("Location: dashboard.php"); 
    exit; 
}

if (isset($_POST['register'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);

    $cek = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($cek->num_rows > 0) {
        $_SESSION['msg'] = ['text' => 'Username sudah terdaftar!', 'type' => 'error'];
    } else {
        $foto_default = "default.png";
        $sql = "INSERT INTO users (full_name, username, email, password, address, profile_picture) 
                VALUES ('$fullname', '$username', '$email', '$password', '$address', '$foto_default')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg'] = ['text' => 'Akun berhasil dibuat! Silakan masuk.', 'type' => 'success'];
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['msg'] = ['text' => 'Gagal: ' . mysqli_error($conn), 'type' => 'error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .logo-heavy-shadow { 
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)) drop-shadow(0 10px 15px rgba(0,0,0,0.2)); 
        }
        .input-standard {
            height: 60px;
        }
        .textarea-standard {
            min-height: 120px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex items-center justify-center p-4 md:p-8">

    <div class="w-full max-w-[500px]">
        <div class="flex flex-col items-center mb-10 text-center">
            <img src="logo.png" alt="Logo" class="w-24 h-24 object-contain mb-4 logo-heavy-shadow">
            <h1 class="text-3xl font-black tracking-tighter text-slate-900 uppercase">
                WANT<span class="text-blue-600">2</span>BUY
            </h1>
        </div>

        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-white overflow-hidden p-8 md:p-12">
            <div class="mb-10 text-left">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Register</h2>
                <p class="text-slate-400 text-sm font-medium">Lengkapi form di bawah ini</p>
            </div>

            <form action="" method="POST" class="space-y-5">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-2">Email</label>
                    <input type="email" name="email" required placeholder="contoh@gmail.com"
                           class="input-standard w-full px-7 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none transition-all font-bold text-slate-700 shadow-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-2">Username</label>
                    <input type="text" name="username" required placeholder="Username"
                           class="input-standard w-full px-7 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none transition-all font-bold text-slate-700 shadow-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-2">Password</label>
                    <div class="relative">
                        <input type="password" id="passwordField" name="password" required placeholder="Password"
                               class="input-standard w-full px-7 pr-16 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none transition-all font-bold text-slate-700 shadow-sm">
                        <button type="button" onclick="togglePassword()" class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-2">Nama Lengkap</label>
                    <input type="text" name="full_name" required placeholder="Nama lengkap"
                           class="input-standard w-full px-7 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none transition-all font-bold text-slate-700 shadow-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-2">Domisili</label>
                    <textarea name="address" required placeholder="Alamat"
                              class="textarea-standard w-full px-7 py-5 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none transition-all font-bold text-slate-700 shadow-sm resize-none"></textarea>
                </div>

                <div class="pt-6">
                    <button type="submit" name="register"
                            class="w-full h-[65px] bg-transparent border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-sm transition-all duration-300 hover:-translate-y-1 active:scale-[0.98]">
                        Daftar
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest">
                    Sudah punya akun?
                    <a href="login.php" class="text-blue-600 ml-2 hover:underline">Log In</a>
                </p>
            </div>
        </div>
    </div>

    <?php include 'toast.php'; ?>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.411m0 0L21 21m-4.225-4.225A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.411m0 0L21 21" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>