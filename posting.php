<?php
session_start();
require_once 'koneksi.php';
set_time_limit(0);

// Proteksi Halaman
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

if (isset($_POST['submit_post'])) {
    $user_id = $_SESSION['user']['id'];
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $min = $_POST['budget_min'];
    $max = $_POST['budget_max'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert ke tabel postings
    $sql_post = "INSERT INTO postings (user_id, product_name, budget_min, budget_max, description) VALUES ('$user_id', '$name', '$min', '$max', '$desc')";
    
    if ($conn->query($sql_post)) {
        $post_id = $conn->insert_id;
        
        // Cek jika ada foto yang diupload
        if (!empty($_FILES['photos']['name'][0])) {
            if (!is_dir('uploads/products/')) {
                mkdir('uploads/products/', 0777, true);
            }

            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp) {
                if ($_FILES['photos']['error'][$key] == 0) {
                    $ext = pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION);
                    $file_name = "IMG_" . uniqid() . "." . $ext;
                    move_uploaded_file($tmp, "uploads/products/" . $file_name);
                    $conn->query("INSERT INTO posting_images (posting_id, image_name) VALUES ('$post_id', '$file_name')");
                }
            }
        }
        
        $_SESSION['toast_msg'] = "Permintaan berhasil dipublikasikan!";
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Permintaan | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { transform: translateY(-2px); shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="text-slate-900 pb-20">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 mb-10">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-slate-400 hover:text-blue-600 transition font-black text-[10px] uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Batal
            </a>
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">Create New Request</span>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-6">
        <div class="bg-white rounded-[3.5rem] p-10 md:p-14 shadow-2xl shadow-slate-200/50 border border-slate-50">
            
            <div class="mb-10">
                <h2 class="text-3xl font-black tracking-tight text-slate-900 mb-2">Cari Barang Apa?</h2>
                <p class="text-slate-400 text-sm font-medium">Beritahu komunitas barang yang Anda butuhkan dan tentukan budgetnya.</p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Barang / Produk</label>
                    <input type="text" name="product_name" placeholder="Contoh: iPhone 13 Pro Max Blue" required 
                           class="w-full px-7 py-5 rounded-[1.8rem] bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none font-bold text-slate-700 input-focus transition-all">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Budget Minimal (Rp)</label>
                        <input type="number" name="budget_min" placeholder="0" required 
                               class="w-full px-7 py-5 rounded-[1.8rem] bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none font-bold text-slate-700 input-focus transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Budget Maksimal (Rp)</label>
                        <input type="number" name="budget_max" placeholder="0" required 
                               class="w-full px-7 py-5 rounded-[1.8rem] bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none font-bold text-slate-700 input-focus transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Detail & Spesifikasi</label>
                    <textarea name="description" rows="5" placeholder="Sebutkan kondisi (mulus/minus), kelengkapan, atau warna yang diinginkan..." 
                              class="w-full px-7 py-5 rounded-[1.8rem] bg-slate-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white outline-none font-medium text-slate-600 text-sm input-focus transition-all"></textarea>
                </div>
                
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Referensi Gambar (Opsional)</label>
                    <div class="relative group">
                        <input type="file" id="photo-input" name="photos[]" multiple accept="image/*" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div class="w-full px-6 py-12 rounded-[2rem] bg-blue-50/40 border-2 border-dashed border-blue-100 text-blue-500 text-center transition-all group-hover:bg-blue-50 group-hover:border-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">Klik atau Seret Gambar Ke Sini</span>
                        </div>
                    </div>
                    <div id="file-list" class="grid grid-cols-3 gap-3"></div>
                </div>

                <button type="submit" name="submit_post" 
                        class="w-full py-6 bg-slate-900 hover:bg-blue-600 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-slate-200 transition-all active:scale-[0.98]">
                    Publikasikan Permintaan
                </button>
            </form>
        </div>
    </div>

    <script>
        const photoInput = document.getElementById('photo-input');
        const fileList = document.getElementById('file-list');

        photoInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            Array.from(photoInput.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = "relative aspect-square rounded-2xl overflow-hidden border-2 border-white shadow-sm";
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/20"></div>
                    `;
                    fileList.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>