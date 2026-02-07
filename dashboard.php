<?php
session_start();
require_once 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// 2. Ambil data profil terbaru
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// 3. Ambil daftar postingan milik user (Fitur Utama)
$sql_posts = "SELECT postings.*, 
              (SELECT image_name FROM posting_images WHERE posting_id = postings.id ORDER BY id ASC LIMIT 1) as main_image
              FROM postings 
              WHERE user_id = ? 
              ORDER BY created_at DESC";

$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $user_id);
$stmt_posts->execute();
$posts_result = $stmt_posts->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Control | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .logo-shadow { filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); }
        /* Style Bold Black Theme */
        .btn-bold { transition: all 0.2s ease; border: 2px solid transparent; }
        .btn-bold:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="text-slate-900">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-white border-r border-slate-100 hidden md:block">
            <div class="p-8 sticky top-0">
                <a href="index.php" class="flex items-center gap-3 mb-10 group">
                    <img src="logo.png" alt="Logo" class="w-8 h-8 object-contain logo-shadow group-hover:scale-110 transition">
                    <span class="text-xl font-black tracking-tighter uppercase">WANT<span class="text-blue-600">2</span>BUY</span>
                </a>
                
                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Postingan Saya
                    </a>
                    <a href="profile.php" class="flex items-center gap-3 p-3 text-slate-400 hover:text-slate-900 transition-all font-black text-[10px] uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Edit Profil
                    </a>
                    <hr class="my-4 border-slate-50">
                    <a href="logout.php" class="flex items-center gap-3 p-3 text-red-400 hover:text-red-600 transition-all font-black text-[10px] uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </a>
                </nav>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div>
                    <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 mb-1">User Dashboard</h2>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">Postingan Saya</h1>
                </div>
                <a href="posting.php" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-blue-100 btn-bold">
                    + Cari Barang Baru
                </a>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-blue-600 text-white px-6 py-4 rounded-2xl mb-8 flex items-center justify-between shadow-lg shadow-blue-100">
                    <span class="text-[10px] font-black uppercase tracking-widest"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                    <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100">✕</button>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 border-b border-slate-100">
                        <tr>
                            <th class="p-8 text-[10px] font-black uppercase tracking-widest text-slate-400">Info Produk</th>
                            <th class="p-8 text-[10px] font-black uppercase tracking-widest text-slate-400 hidden md:table-cell">Budget Maks</th>
                            <th class="p-8 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if ($posts_result->num_rows > 0): ?>
                            <?php while($row = $posts_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/30 transition-colors group">
                                    <td class="p-8">
                                        <div class="flex items-center gap-6">
                                            <div class="w-20 h-20 rounded-[1.5rem] bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-100 shadow-sm">
                                                <?php 
                                                    $img = (!empty($row['main_image']) && file_exists("uploads/products/".$row['main_image'])) 
                                                           ? "uploads/products/".$row['main_image'] 
                                                           : "https://ui-avatars.com/api/?name=".urlencode($row['product_name'])."&background=f1f5f9&color=64748b"; 
                                                ?>
                                                <img src="<?= $img ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                            </div>
                                            <div>
                                                <p class="font-black text-lg text-slate-900 mb-1 leading-tight"><?= htmlspecialchars($row['product_name']) ?></p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Post: <?= date('d M Y', strtotime($row['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-8 hidden md:table-cell">
                                        <span class="px-4 py-2 bg-slate-100 rounded-lg text-[11px] font-black text-slate-900 italic">
                                            Rp <?= number_format($row['budget_max'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="p-8">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="edit_post.php?id=<?= $row['id'] ?>" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </a>
                                            <a href="hapus_post.php?id=<?= $row['id'] ?>" 
                                               onclick="return confirm('Hapus postingan ini secara permanen?')"
                                               class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="p-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 italic font-black text-2xl">?</div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Belum ada postingan aktif</p>
                                        <a href="posting.php" class="text-blue-600 font-black text-[10px] uppercase tracking-widest border-b-2 border-blue-600 pb-1">Buat Sekarang</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>
