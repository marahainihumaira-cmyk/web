<?php
include 'koneksi.php';

$pesan_error = "";
if (isset($_POST['simpan_toko'])) {
    $nama_toko   = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tema_warna  = mysqli_real_escape_string($koneksi, $_POST['tema_warna']);
    
    $nama_file = $_FILES['foto_toko']['name'];
    $tmp_file  = $_FILES['foto_toko']['tmp_name'];
    
    if (!empty($nama_file)) {
        $foto_toko = time() . '_' . basename($nama_file);
        move_uploaded_file($tmp_file, 'gambar/' . $foto_toko);
    } else {
        $foto_toko = 'toko_default.jpg';
    }

    $query = "INSERT INTO toko (nama_toko, deskripsi, foto_toko, tema_warna) 
              VALUES ('$nama_toko', '$deskripsi', '$foto_toko', '$tema_warna')";
    
    if (mysqli_query($koneksi, $query)) {
        $id_baru = mysqli_insert_id($koneksi);
        header("Location: owner.php?id=$id_baru");
        exit();
    } else {
        $pesan_error = "Gagal mendaftarkan toko: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko Baru - Umai Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .header-form { background: linear-gradient(135deg, #ff758c, #ff7eb3); color: white; padding: 40px 0; border-radius: 0 0 30px 30px; margin-bottom: 30px; }
        .card-form { border: none; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="header-form text-center">
        <div class="container">
            <h1 class="fw-bold">🏪 Buka Toko Baru di Umai Mart</h1>
            <p class="mb-0">Daftarkan tokomu dan mulai jualan!</p>
            <a href="index.php" class="btn btn-light btn-sm rounded-pill mt-3 px-4 fw-bold">← Kembali ke Halaman Utama</a>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (!empty($pesan_error)) { ?>
                    <div class="alert alert-danger rounded-4 shadow-sm"><?php echo $pesan_error; ?></div>
                <?php } ?>

                <div class="card card-form p-4 bg-white">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Toko</label>
                            <input type="text" name="nama_toko" class="form-control" required placeholder="Contoh: Umai Snack">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Toko</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Ceritakan singkat tentang tokomu..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tema Warna Toko</label>
                            <input type="color" name="tema_warna" class="form-control form-control-color w-100" value="#ff4757">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Foto Toko</label>
                            <input type="file" name="foto_toko" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" name="simpan_toko" class="btn btn-danger w-100 py-2 fw-bold rounded-pill shadow-sm">
                            Buat Toko Sekarang 🚀
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>