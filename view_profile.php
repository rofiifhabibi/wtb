<?php
session_start();
require_once 'koneksi.php';

// Ambil ID user dari URL
$target_user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($target_user_id === 0) {
    header("Location: index.php");
    exit;
}

// 1. Ambil data profil user
// Saya tambahkan pengecekan agar tidak error jika kolom belum siap
$stmt = $conn->prepare("SELECT full_name, profile_picture, address, whatsapp, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $target_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<script>alert('User tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// 2. Ambil daftar barang yang ingin dibeli user ini
$query_posts = "SELECT postings.*, 
               (SELECT image_name FROM posting_images WHERE posting_id = postings.id ORDER BY id ASC LIMIT 1) as main_image 
               FROM postings 
               WHERE user_id = ? 
               ORDER BY created_at DESC";
$stmt_posts = $conn->prepare($query_posts);
$stmt_posts->bind_param("i", $target_user_id);
$stmt_posts->execute();
$posts = $stmt_posts->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?= htmlspecialchars((string)$user['full_name']) ?> | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .card-profile { transition: all 0.3s ease; }
        .card-profile:hover { transform: translateY(-5px); shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
    </style>
</head>
<body class="text-slate-900">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-slate-400 hover:text-blue-600 transition font-black text-[10px] uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Feed
            </a>
            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300 italic">User Public Profile</span>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        
        <div class="bg-white rounded-[3.5rem] p-8 md:p-12 shadow-2xl shadow-slate-200/60 border border-slate-50 mb-16 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-50 rounded-full opacity-50"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                <div class="relative">
                    <?php 
                        $p_img = (!empty($user['profile_picture']) && file_exists("uploads/" . $user['profile_picture'])) 
                                 ? "uploads/" . $user['profile_picture'] : "";
                    ?>
                    <img src="<?= $p_img ?>" 
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode((string)$user['full_name']) ?>&background=0284c7&color=fff&size=200'" 
                         class="w-40 h-40 rounded-[3rem] object-cover shadow-2xl border-4 border-white">
                    <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white"></div>
                </div>

                <div class="flex-grow text-center md:text-left">
                    <div class="mb-4">
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-1"><?= htmlspecialchars((string)$user['full_name']) ?></h1>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest flex items-center justify-center md:justify-start gap-2">
                            Member Sejak <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?>
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        <div class="px-4 py-2 bg-slate-50 rounded-xl flex items-center gap-2 border border-slate-100">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                            <span class="text-[11px] font-black uppercase text-slate-500 tracking-wider"><?= htmlspecialchars((string)($user['address'] ?? 'Lokasi Rahasia')) ?></span>
                        </div>
                    </div>
                </div>

                <?php if(!empty($user['whatsapp'])): ?>
                <div class="shrink-0">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $user['whatsapp']) ?>" target="_blank" class="flex flex-col items-center gap-3 group">
                        <div class="w-16 h-16 bg-green-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-green-200 group-hover:scale-110 group-hover:bg-green-600 transition-all">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 group-hover:text-green-500">Kirim Penawaran</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center justify-between mb-8 px-4">
            <h2 class="text-xs font-black uppercase tracking-[0.4em] text-slate-400">Wishlist Items</h2>
            <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                <?= $posts->num_rows ?> Items
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($posts->num_rows > 0): ?>
                <?php while($row = $posts->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2.5rem] p-5 border border-slate-50 shadow-xl shadow-slate-200/30 flex flex-col group card-profile">
                        <div class="aspect-video rounded-2xl overflow-hidden mb-5 bg-slate-50 relative">
                            <?php if($row['main_image']): ?>
                                <img src="uploads/products/<?= $row['main_image'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center opacity-20">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="px-2 mb-6">
                            <h3 class="font-black text-slate-900 mb-1 leading-tight"><?= htmlspecialchars((string)$row['product_name']) ?></h3>
                            <p class="text-[10px] font-bold text-blue-600 italic">
                                Rp <?= number_format($row['budget_min'], 0, ',', '.') ?> - <?= number_format($row['budget_max'], 0, ',', '.') ?>
                            </p>
                        </div>

                        <a href="detail.php?id=<?= $row['id'] ?>" class="mt-auto bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all text-center">
                            Lihat Detail
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-100 flex flex-col items-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-slate-300 font-black uppercase text-[10px] tracking-widest">Belum ada postingan dari user ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>