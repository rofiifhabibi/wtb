<?php
session_start();
require_once 'koneksi.php';

// 1. Cek status login
$is_logged_in = isset($_SESSION['user']);
$user_session = $is_logged_in ? $_SESSION['user'] : null;

// 2. Ambil data profil terbaru dari DB untuk Navbar
$current_user_photo = "";
$current_user_name = "User";
if ($is_logged_in) {
    $uid = $user_session['id'];
    $stmt = $conn->prepare("SELECT profile_picture, full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();
    $current_user_photo = $userData['profile_picture'] ?? '';
    $current_user_name = $userData['full_name'] ?? 'User';
}

// 3. Query Feed
$sql = "SELECT postings.*, 
               users.full_name as buyer_name, 
               users.address as buyer_address, 
               users.profile_picture as buyer_photo,
               (SELECT image_name FROM posting_images WHERE posting_id = postings.id ORDER BY id ASC LIMIT 1) as main_image
        FROM postings 
        JOIN users ON postings.user_id = users.id 
        ORDER BY postings.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Want2Buy | Community Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        
        /* Efek Bayangan Postingan agar lebih tegas */
        .post-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 20px 25px -5px rgba(0, 0, 0, 0.02);
        }
        
        .post-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border-color: rgba(59, 130, 246, 0.2); /* Border biru tipis saat hover */
        }

        .logo-heavy-shadow { filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2)); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="text-slate-900">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center">
                <a href="index.php" class="flex items-center gap-3">
                    <img src="logo.png" alt="Logo" class="w-10 h-10 object-contain logo-heavy-shadow">
                    <span class="text-xl font-black tracking-tighter text-slate-900 uppercase">
                        WANT<span class="text-blue-600">2</span>BUY
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <?php if ($is_logged_in): ?>
                    <div class="flex items-center gap-3 pl-6 border-l border-slate-100">
                        <div class="text-right hidden sm:block">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Halo,</p>
                            <p class="text-sm font-bold text-slate-900 leading-none"><?= htmlspecialchars((string)$current_user_name) ?></p>
                        </div>
                        
                        <a href="dashboard.php" class="relative group">
                            <?php 
                                $final_header_img = (!empty($current_user_photo) && file_exists("uploads/" . $current_user_photo)) 
                                                    ? "uploads/" . $current_user_photo : "";
                            ?>
                            <img src="<?= $final_header_img ?>" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode((string)$current_user_name) ?>&background=0284c7&color=fff'" 
                                 class="w-11 h-11 rounded-full border-2 border-white shadow-md object-cover transition-transform group-hover:scale-110 active:scale-95">
                        </a>

                        <a href="logout.php" class="p-2 text-slate-300 hover:text-red-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-lg">Log / Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.4em] text-blue-600 mb-2">Marketplace Feed</h2>
                <h3 class="text-3xl font-black text-slate-900 tracking-tight">Apa yang sedang dicari?</h3>
            </div>
            <?php if($is_logged_in): ?>
                <a href="posting.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">Buat Postingan</a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2.8rem] p-5 border border-slate-100 post-card flex flex-col group">
                        
                        <div class="relative aspect-video rounded-[2rem] overflow-hidden mb-6 bg-slate-100">
                            <?php 
                                $product_img = (!empty($row['main_image']) && file_exists("uploads/products/" . $row['main_image'])) 
                                               ? "uploads/products/" . $row['main_image'] : ""; 
                            ?>
                            <?php if(!empty($product_img)): ?>
                                <img src="<?= $product_img ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                    <svg class="w-8 h-8 mb-1 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest">No Image</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-slate-900 text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <?= htmlspecialchars((string)($row['buyer_address'] ?? 'Area COD')) ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex-grow px-2">
                            <h4 class="text-xl font-extrabold text-slate-900 mb-2 leading-tight group-hover:text-blue-600 transition-colors"><?= htmlspecialchars((string)($row['product_name'] ?? 'Produk')) ?></h4>
                            <p class="text-slate-400 text-xs leading-relaxed line-clamp-2 mb-6 font-medium italic">"<?= htmlspecialchars((string)($row['description'] ?? '')) ?>"</p>
                        </div>

                        <div class="px-2 mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100/50 group-hover:bg-blue-50/50 transition-colors">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Budget Range
                            </p>
                            <p class="text-base font-black text-blue-600 italic leading-none">
                                Rp <?= number_format((float)($row['budget_min'] ?? 0), 0, ',', '.') ?> - <?= number_format((float)($row['budget_max'] ?? 0), 0, ',', '.') ?>
                            </p>
                        </div>

                        <div class="pt-6 border-t border-slate-50 flex items-center justify-between px-2">
                            <a href="view_profile.php?id=<?= $row['user_id'] ?>" class="flex items-center gap-3 group/user">
                                <?php 
                                    $b_photo = (!empty($row['buyer_photo']) && file_exists("uploads/" . $row['buyer_photo'])) 
                                               ? "uploads/" . $row['buyer_photo'] : ""; 
                                ?>
                                <div class="relative">
                                    <img src="<?= $b_photo ?>" 
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode((string)($row['buyer_name'] ?? 'User')) ?>&background=random'" 
                                         class="w-9 h-9 rounded-full border-2 border-white shadow-sm object-cover group-hover/user:ring-2 group-hover/user:ring-blue-500 transition-all duration-300 group-hover/user:scale-110">
                                </div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover/user:text-blue-600 transition-colors truncate max-w-[80px]">
                                    <?= htmlspecialchars((string)($row['buyer_name'] ?? 'User')) ?>
                                </span>
                            </a>
                            
                            <?php if ($is_logged_in && $row['user_id'] == $user_session['id']): ?>
                                <a href="edit_post.php?id=<?= $row['id'] ?>" 
                                   class="bg-blue-50 text-blue-600 px-5 py-3 rounded-xl text-[9px] font-black hover:bg-blue-600 hover:text-white transition-all uppercase tracking-widest flex items-center gap-2 shrink-0 border border-blue-100">
                                    Manage
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            <?php else: ?>
                                <a href="<?= $is_logged_in ? 'detail.php?id='.$row['id'] : 'login.php' ?>" 
                                   class="bg-slate-900 text-white px-5 py-3 rounded-xl text-[9px] font-black hover:bg-blue-600 transition-all uppercase tracking-widest flex items-center gap-2 shrink-0 shadow-lg shadow-slate-200 group-hover:shadow-blue-200">
                                    Tawarkan
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 flex flex-col items-center opacity-30">
                    <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="font-black uppercase tracking-[0.3em] text-[10px]">Belum ada permintaan barang.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>