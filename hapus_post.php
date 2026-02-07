<?php
session_start();
require_once 'koneksi.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$post_id = $_GET['id'] ?? null;

if ($post_id) {
    // 1. Cek apakah postingan ini memang milik user yang login (Keamanan)
    $stmt = $conn->prepare("SELECT id FROM postings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Ambil nama file gambar untuk dihapus dari folder
        $img_stmt = $conn->prepare("SELECT image_name FROM posting_images WHERE posting_id = ?");
        $img_stmt->bind_param("i", $post_id);
        $img_stmt->execute();
        $images = $img_stmt->get_result();

        while ($img = $images->fetch_assoc()) {
            $file_path = "uploads/products/" . $img['image_name'];
            if (file_exists($file_path)) {
                unlink($file_path); // Menghapus file fisik
            }
        }

        // 3. Hapus data dari database (Gunakan transaksional jika perlu)
        // Karena ada foreign key, pastikan tabel images terhapus otomatis (ON DELETE CASCADE) 
        // atau hapus manual tabel images dulu baru tabel postings.
        $conn->query("DELETE FROM posting_images WHERE posting_id = $post_id");
        $conn->query("DELETE FROM postings WHERE id = $post_id");

        $_SESSION['success'] = "Postingan berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Anda tidak memiliki akses untuk menghapus postingan ini.";
    }
}

header("Location: dashboard.php");
exit();
