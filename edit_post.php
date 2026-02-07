<?php
session_start();
require_once 'koneksi.php';

// 1. PROTEKSI: Harus login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. AMBIL DATA LAMA
$query = $conn->query("SELECT * FROM postings WHERE id = '$post_id' AND user_id = '$user_id'");
$data = $query->fetch_assoc();

if (!$data) {
    die("<div class='h-screen flex items-center justify-center font-bold text-slate-500'>Postingan tidak ditemukan atau akses ditolak.</div>");
}

// 3. PROSES UPDATE DATA
if (isset($_POST['update_post'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $budget_min = $_POST['budget_min'];
    $budget_max = $_POST['budget_max'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql_update = "UPDATE postings SET 
                   product_name = '$product_name', 
                   budget_min = '$budget_min', 
                   budget_max = '$budget_max', 
                   description = '$description' 
                   WHERE id = '$post_id'";

    if ($conn->query($sql_update)) {
        if (!empty($_FILES['photos']['name'][0])) {
            $upload_dir = "uploads/products/";
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION);
                    $new_file_name = "IMG_" . uniqid() . "_" . time() . "." . $extension;
                    if (move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
                        $conn->query("INSERT INTO posting_images (posting_id, image_name) VALUES ('$post_id', '$new_file_name')");
                    }
                }
            }
        }
        $_SESSION['toast_msg'] = "Perubahan berhasil disimpan!";
        header("Location: dashboard.php");
        exit;
    }
}

// 4. PROSES HAPUS FOTO
if (isset($_GET['delete_img'])) {
    $img_id = $_GET['delete_img'];
    $img_name = $_GET['img_name'];
    if ($conn->query("DELETE FROM posting_images WHERE id = '$img_id' AND posting_id = '$post_id'")) {
        if(file_exists("uploads/products/" . $img_name)) {
            unlink("uploads/products/" . $img_name);
        }
        header("Location: edit_post.php?id=" . $post_id);
        exit;
    }
}

$images_query = $conn->query("SELECT * FROM posting_images WHERE posting_id = '$post_id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Request | Want2Buy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.1); }
    </style>
</head>
<body class="text-slate-900 pb-20">

    <div class="max-w-4xl mx-auto py-12 px-6">
        <a href="dashboard.php" class="inline-flex items-center gap-2 text-slate-400 hover:text-blue-600 font-black text-[10px] uppercase tracking-[0.3em] mb-10 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Dashboard
        </a>

        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/60 overflow-hidden border border-white">
            <div class="p-8 md:p-14">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 leading-none">Edit Permintaan</h1>
                        <p class="text-slate-400 text-sm mt-2 font-medium">Perbarui spesifikasi barang yang Anda inginkan</p>
                    </div>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" class="space-y-10">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Nama Produk yang Dicari</label>
                            <input type="text" name="product_name" value="<?= htmlspecialchars($data['product_name']) ?>" required
                                   class="input-focus w-full px-7 py-5 rounded-[1.5rem] bg-slate-50 border-2 border-transparent focus:bg-white focus:border-blue-500/20 outline-none font-bold text-slate-800 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Budget Minimal (Rp)</label>
                            <input type="number" name="budget_min" value="<?= $data['budget_min'] ?>" required
                                   class="input-focus w-full px-7 py-5 rounded-[1.5rem] bg-slate-50 border-2 border-transparent focus:bg-white focus:border-blue-500/20 outline-none font-bold text-slate-800 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Budget Maksimal (Rp)</label>
                            <input type="number" name="budget_max" value="<?= $data['budget_max'] ?>" required
                                   class="input-focus w-full px-7 py-5 rounded-[1.5rem] bg-slate-50 border-2 border-transparent focus:bg-white focus:border-blue-500/20 outline-none font-bold text-slate-800 shadow-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Deskripsi & Kriteria</label>
                            <textarea name="description" rows="5" required
                                      class="input-focus w-full px-7 py-5 rounded-[1.5rem] bg-slate-50 border-2 border-transparent focus:bg-white focus:border-blue-500/20 outline-none font-medium text-slate-600 leading-relaxed shadow-sm"><?= htmlspecialchars($data['description']) ?></textarea>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Kelola Foto Referensi</label>
                            <span class="text-[9px] font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-full italic">Klik ikon merah untuk menghapus</span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                            <?php while($img = $images_query->fetch_assoc()): ?>
                            <div class="relative group aspect-square rounded-[2rem] overflow-hidden border-4 border-slate-50 shadow-sm">
                                <img src="uploads/products/<?= $img['image_name'] ?>" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <a href="edit_post.php?id=<?= $post_id ?>&delete_img=<?= $img['id'] ?>&img_name=<?= $img['image_name'] ?>" 
                                       onclick="return confirm('Hapus foto ini dari sistem?')"
                                       class="bg-white text-red-500 p-3 rounded-2xl shadow-xl hover:scale-110 transition active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>

                            <div class="relative group aspect-square rounded-[2rem] border-2 border-dashed border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 transition cursor-pointer overflow-hidden">
                                <input type="file" id="photo-input" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-slate-400 group-hover:text-blue-500 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Tambah Foto</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="file" name="photos[]" id="final-photos" multiple class="hidden">
                    
                    <div id="file-list" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>

                    <div class="flex flex-col md:flex-row gap-4 pt-6 border-t border-slate-50">
                        <button type="submit" name="update_post"
                                class="flex-1 py-6 bg-slate-900 hover:bg-blue-600 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-slate-200 transition-all active:scale-[0.97]">
                            Simpan Perubahan
                        </button>
                        <a href="dashboard.php" class="py-6 px-10 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] text-center transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const photoInput = document.getElementById('photo-input');
        const finalInput = document.getElementById('final-photos');
        const fileList = document.getElementById('file-list');
        let allFiles = new DataTransfer();

        photoInput.addEventListener('change', function() {
            Array.from(photoInput.files).forEach(file => {
                allFiles.items.add(file);
            });
            finalInput.files = allFiles.files;
            renderList();
        });

        function renderList() {
            fileList.innerHTML = '';
            Array.from(allFiles.files).forEach((file, index) => {
                const item = document.createElement('div');
                item.className = "flex items-center gap-4 bg-blue-50/50 border border-blue-100/50 p-4 rounded-2xl animate-in slide-in-from-bottom-2 duration-300";
                item.innerHTML = `
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex-shrink-0 flex items-center justify-center text-white shadow-md shadow-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="flex-1 min-w-0"> 
                        <p class="text-[10px] font-black text-slate-800 truncate mb-0 uppercase tracking-wider">${file.name}</p>
                        <p class="text-[9px] font-bold text-blue-400 uppercase italic">Siap diupload</p>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"/></svg>
                    </button>
                `;
                fileList.appendChild(item);
            });
        }

        function removeFile(index) {
            const newTransfer = new DataTransfer();
            Array.from(allFiles.files).forEach((file, i) => {
                if(i !== index) newTransfer.items.add(file);
            });
            allFiles = newTransfer;
            finalInput.files = allFiles.files;
            renderList();
        }
    </script>

    <?php if(isset($_SESSION['toast_msg'])): ?>
        <?php include 'toast.php'; ?>
    <?php endif; ?>
</body>
</html>