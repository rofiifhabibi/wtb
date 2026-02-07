<?php
session_start();
require_once 'koneksi.php';

// Proteksi Halaman: Jika belum login, tendang ke login.php
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Ambil data profil terbaru dari DB
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Hitung statistik user (Contoh: jumlah postingan milik sendiri)
$count_posts = $conn->query("SELECT COUNT(*) as total FROM postings WHERE user_id = '$user_id'")->fetch_assoc();

// Ambil postingan milik user ini saja
$my_posts = $conn->query("SELECT postings.*, 
                         (SELECT image_name FROM posting_images WHERE posting_id = postings.id ORDER BY id ASC LIMIT 1) as main_image 
                         FROM postings WHERE user_id = '$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Want2Buy</title>
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
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3">
                    <img src="logo.png" alt="Logo" class="w-10 h-10 object-contain logo-heavy-shadow">
                    <span class="text-xl font-black tracking-tighter text-slate-900 uppercase">
                        WANT<span class="text-blue-600">2</span>BUY
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="index.php" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 transition">Back to Feed</a>
                <a href="logout.php" class="p-2 text-slate-300 hover:text-red-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        <div class="bg-white rounded-[3rem] p-8 md:p-12 shadow-xl shadow-slate-200/50 border border-slate-50 mb-10 flex flex-col md:flex-row items-center gap-8">
            <div class="relative">
                <?php 
                    $profile_img = (!empty($user['profile_picture']) && file_exists("uploads/" . $user['profile_picture'])) 
                                   ? "uploads/" . $user['profile_picture'] : "";
                ?>
                <img src="<?= $profile_img ?>" 
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=0284c7&color=fff'" 
                     class="w-32 h-32 rounded-[2.5rem] object-cover border-4 border-slate-50 shadow-lg shadow-blue-100">
                <div class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2.5 rounded-2xl shadow-lg border-4 border-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <div class="flex-grow text-center md:text-left">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 block">Verified Member</span>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2"><?= htmlspecialchars($user['full_name']) ?></h1>
                <p class="text-slate-400 font-medium mb-6"><?= htmlspecialchars($user['address'] ?? 'Alamat belum diatur') ?></p>
                
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                    <a href="profile.php" class="px-8 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95">
                        Edit Profile
                    </a>
                    <div class="h-10 w-[1px] bg-slate-100 hidden md:block mx-2"></div>
                    <div class="text-left">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1">Total Posts</p>
                        <p class="text-lg font-black text-slate-900 leading-none"><?= $count_posts['total'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-8 px-2">
            <h2 class="text-xl font-black text-slate-900 tracking-tight uppercase tracking-widest text-sm">Postingan Saya</h2>
            <a href="posting.php" class="text-[10px] font-black text-blue-600 hover:text-slate-900 uppercase tracking-widest transition">+ Tambah Baru</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($my_posts->num_rows > 0): ?>
                <?php while($post = $my_posts->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2.5rem] p-6 border border-slate-50 shadow-xl shadow-slate-200/40 flex flex-col group">
                        <div class="relative aspect-video rounded-3xl overflow-hidden mb-5 bg-slate-50">
                            <?php if(!empty($post['main_image'])): ?>
                                <img src="uploads/products/<?= $post['main_image'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-200 uppercase text-[9px] font-black">No Photo</div>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="font-extrabold text-slate-900 mb-2 truncate px-2"><?= htmlspecialchars($post['product_name']) ?></h3>
                        
                        <div class="mt-auto pt-5 border-t border-slate-50 flex items-center justify-between px-2">
                            <a href="detail.php?id=<?= $post['id'] ?>" class="text-[10px] font-black text-slate-400 hover:text-blue-600 uppercase tracking-widest transition">View Detail</a>
                            <div class="flex gap-2">
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="p-2 text-slate-300 hover:text-blue-500 transition"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                                <a href="delete_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Hapus postingan ini?')" class="p-2 text-slate-300 hover:text-red-500 transition"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-100 text-center">
                    <p class="text-slate-300 font-black uppercase tracking-widest text-[10px]">Anda belum membuat postingan apapun.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>