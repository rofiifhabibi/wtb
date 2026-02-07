<?php
session_start();
require_once 'koneksi.php';

// 1. PROTEKSI: Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

// 2. AMBIL ID DARI URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_post = mysqli_real_escape_string($conn, $_GET['id']);

// 3. QUERY AMBIL DATA POSTINGAN & USER
$sql = "SELECT postings.*, users.full_name, users.profile_picture, users.whatsapp, users.id as buyer_id
        FROM postings 
        JOIN users ON postings.user_id = users.id 
        WHERE postings.id = '$id_post'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

if (!$data) {
    die("Postingan tidak ditemukan!");
}

// 4. QUERY AMBIL SEMUA GAMBAR
$images_query = $conn->query("SELECT image_name FROM posting_images WHERE posting_id = '$id_post'");
$images = [];
while($img = $images_query->fetch_assoc()) {
    $images[] = $img['image_name'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['product_name']) ?> | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .swiper { width: 100%; height: 100%; border-radius: 2.5rem; }
        .swiper-slide img { width: 100%; height: 500px; object-fit: cover; cursor: zoom-in; }
        .swiper-pagination-bullet-active { background: #2563eb !important; }
        .swiper-button-next, .swiper-button-prev { color: white !important; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    </style>
</head>
<body class="text-slate-900 pb-20">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 mb-8">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-slate-400 hover:text-blue-600 transition font-black text-[10px] uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 italic">Request Detail</span>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            
            <div class="relative group">
                <div class="swiper mySwiper shadow-2xl shadow-slate-200">
                    <div class="swiper-wrapper">
                        <?php if(count($images) > 0): ?>
                            <?php foreach($images as $img_name): ?>
                                <div class="swiper-slide bg-slate-100">
                                    <a href="uploads/products/<?= $img_name ?>" data-fancybox="gallery" data-caption="<?= htmlspecialchars($data['product_name']) ?>">
                                        <img src="uploads/products/<?= $img_name ?>" alt="Produk">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="swiper-slide bg-slate-100 flex items-center justify-center h-[500px]">
                                <div class="text-center opacity-20">
                                    <svg class="w-20 h-20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="font-black uppercase tracking-widest text-xs">No Image Reference</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <p class="text-[9px] text-center text-slate-400 font-bold uppercase tracking-widest mt-4">Klik gambar untuk memperbesar</p>
            </div>

            <div class="space-y-8">
                <div class="space-y-4">
                    <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-100">
                        Buying Request
                    </span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        <?= htmlspecialchars($data['product_name']) ?>
                    </h1>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-slate-200 flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-600 rounded-full blur-3xl opacity-50"></div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-slate-400 mb-2 relative z-10">Target Budget Range</p>
                    <h2 class="text-3xl font-black text-blue-400 relative z-10">
                        Rp <?= number_format($data['budget_min'], 0, ',', '.') ?> - <?= number_format($data['budget_max'], 0, ',', '.') ?>
                    </h2>
                </div>

                <a href="view_profile.php?id=<?= $data['buyer_id'] ?>" class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:border-blue-500 transition group">
                    <?php 
                        $profile_img = (!empty($data['profile_picture']) && file_exists("uploads/" . $data['profile_picture'])) 
                                       ? "uploads/" . $data['profile_picture'] : "";
                    ?>
                    <img src="<?= $profile_img ?>" 
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($data['full_name']) ?>&background=random'" 
                         class="w-14 h-14 rounded-2xl object-cover shadow-sm group-hover:scale-110 transition">
                    <div class="flex-grow">
                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Requested by</p>
                        <p class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition"><?= $data['full_name'] ?></p>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-xl text-slate-300 group-hover:text-blue-500 group-hover:bg-blue-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>

                <div class="space-y-3">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Detail Kebutuhan</h4>
                    <div class="bg-white p-7 rounded-[2rem] border border-slate-50 text-slate-600 leading-relaxed text-sm font-medium shadow-sm italic">
                        "<?= nl2br(htmlspecialchars($data['description'])) ?>"
                    </div>
                </div>

                <div class="pt-4">
                    <?php 
                        $wa_number = preg_replace('/[^0-9]/', '', $data['whatsapp'] ?? '');
                        $wa_text = urlencode("Halo " . $data['full_name'] . ", saya melihat postingan Anda di Want2Buy mengenai: " . $data['product_name'] . ". Saya punya barang yang sesuai.");
                    ?>
                    <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_text ?>" target="_blank" 
                       class="flex items-center justify-center gap-3 w-full py-6 bg-green-500 hover:bg-green-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-green-100 transition-all active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Hubungi WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    
    <script>
        // Inisialisasi Swiper Slider
        var swiper = new Swiper(".mySwiper", {
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
        });

        // Inisialisasi Fancybox untuk Zoom
        Fancybox.bind("[data-fancybox]", {
            // Opsi tambahan bisa ditaruh di sini
            compact: false,
            idle: false,
            animated: true,
            showClass: "f-fadeIn",
        });
    </script>
</body>
</html>