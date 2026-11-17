<?php
include 'koneksi.php';

$id_toko = isset($_GET['id']) ? intval($_GET['id']) : 1;

$query_toko = mysqli_query($koneksi, "SELECT * FROM toko WHERE id_toko = $id_toko");
$toko = mysqli_fetch_assoc($query_toko);

if (!$toko) {
    die("<h3 style='text-align:center; margin-top:50px;'>Toko tidak ditemukan!</h3>");
}

// Proses Tambah Menu Baru
if (isset($_POST['tambah_menu'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $varian_rasa = mysqli_real_escape_string($koneksi, $_POST['varian_rasa']);
    $harga       = intval($_POST['harga']);
    $stok        = intval($_POST['stok']);

    $nama_file = $_FILES['gambar']['name'];
    $tmp_file  = $_FILES['gambar']['tmp_name'];
    
    if (!empty($nama_file)) {
        $gambar_menu = time() . '_' . basename($nama_file);
        move_uploaded_file($tmp_file, 'gambar/' . $gambar_menu);
    } else {
        $gambar_menu = 'menu.jpg';
    }

    mysqli_query($koneksi, "INSERT INTO umaimart (id_toko, nama_produk, kategori, varian_rasa, harga, stok, gambar) 
                            VALUES ($id_toko, '$nama_produk', '$kategori', '$varian_rasa', $harga, $stok, '$gambar_menu')");
    header("Location: owner.php?id=$id_toko");
    exit();
}

// Proses Hapus Menu
if (isset($_GET['hapus_menu'])) {
    $id_menu = intval($_GET['hapus_menu']);
    mysqli_query($koneksi, "DELETE FROM umaimart WHERE no = $id_menu AND id_toko = $id_toko");
    header("Location: owner.php?id=$id_toko");
    exit();
}

// Proses Hapus Toko
if (isset($_GET['hapus_toko'])) {
    mysqli_query($koneksi, "DELETE FROM umaimart WHERE id_toko = $id_toko");
    mysqli_query($koneksi, "DELETE FROM toko WHERE id_toko = $id_toko");
    header("Location: index.php");
    exit();
}

// Proses Ubah Status Pesanan
if (isset($_POST['ubah_status'])) {
    $id_pesanan = intval($_POST['id_pesanan']);
    $status_baru = mysqli_real_escape_string($koneksi, $_POST['status']);
    mysqli_query($koneksi, "UPDATE pesanan SET status = '$status_baru' WHERE id_pesanan = $id_pesanan");
    header("Location: owner.php?id=$id_toko");
    exit();
}

$query_menu = mysqli_query($koneksi, "SELECT * FROM umaimart WHERE id_toko = $id_toko");
$query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_toko = $id_toko ORDER BY tanggal_pesan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Owner - <?php echo $toko['nama_toko']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .owner-header {
            background-color: <?php echo $toko['tema_warna']; ?>;
            color: white; padding: 40px 0; border-radius: 0 0 30px 30px; margin-bottom: 30px;
        }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-img { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

    <div class="owner-header text-center">
        <div class="container">
            <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill fw-bold">👑 Panel Owner Toko</span>
            <h1 class="fw-bold mb-1"><?php echo $toko['nama_toko']; ?></h1>
            <p class="mb-0"><?php echo $toko['deskripsi']; ?></p>
            <div class="mt-3">
                <a href="index.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">← Kembali ke Mall Utama</a>
                <a href="toko.php?id=<?php echo $id_toko; ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold ms-2" target="_blank">👁️ Lihat Toko</a>
                <a href="owner.php?id=<?php echo $id_toko; ?>&hapus_toko=1" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold ms-2" onclick="return confirm('Yakin ingin menghapus toko ini beserta seluruh menu dan pesanannya?')">🗑️ Hapus Toko Ini</a>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        
        <!-- Tambah Menu & Daftar Menu -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card card-custom p-4 bg-white">
                    <h4 class="fw-bold text-dark mb-3">➕ Tambah Menu & Kategori</h4>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Nama Produk / Menu</label>
                            <input type="text" name="nama_produk" class="form-control form-control-sm" required placeholder="Contoh: Es Teh">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Kategori (Misal: Temen Fokus)</label>
                            <input type="text" name="kategori" class="form-control form-control-sm" required placeholder="Nama Kategori">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Varian Rasa (Pisahkan koma)</label>
                            <input type="text" name="varian_rasa" class="form-control form-control-sm" required placeholder="Original, Manis">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-semibold">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control form-control-sm" required placeholder="5000">
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-semibold">Stok</label>
                                <input type="number" name="stok" class="form-control form-control-sm" value="50" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Foto Menu</label>
                            <input type="file" name="gambar" class="form-control form-control-sm" accept="image/*" required>
                        </div>
                        <button type="submit" name="tambah_menu" class="btn btn-dark w-100 btn-sm py-2 fw-bold rounded-pill">
                            Simpan Menu 🚀
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom p-4 bg-white">
                    <h4 class="fw-bold text-dark mb-3">🍔 Daftar Menu di Toko Ini</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th>Foto</th>
                                    <th>Menu</th>
                                    <th>Kategori</th>
                                    <th>Varian</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($query_menu) > 0) { 
                                    while($m = mysqli_fetch_assoc($query_menu)) { ?>
                                <tr class="small">
                                    <td><img src="gambar/<?php echo $m['gambar']; ?>" class="table-img" alt="Img" onerror="this.src='https://via.placeholder.com/45?text=Img'"></td>
                                    <td class="fw-bold"><?php echo $m['nama_produk']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $m['kategori']; ?></span></td>
                                    <td><?php echo $m['varian_rasa']; ?></td>
                                    <td class="text-success fw-bold">Rp <?php echo number_format($m['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $m['stok']; ?></td>
                                    <td class="text-center">
                                        <a href="owner.php?id=<?php echo $id_toko; ?>&hapus_menu=<?php echo $m['no']; ?>" 
                                           class="btn btn-outline-danger btn-sm px-2 py-1" onclick="return confirm('Hapus menu ini?')">🗑️</a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada menu. Silakan tambah lewat form di samping!</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Pesanan Masuk -->
        <div class="card card-custom p-4 bg-white">
            <h4 class="fw-bold text-dark mb-3">📦 Daftar Pesanan Masuk</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">Pemesan</th>
                            <th class="py-3">Detail Produk & Alamat</th>
                            <th class="py-3">Total Harga</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Aksi & Chat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query_pesanan) > 0) {
                            while($p = mysqli_fetch_assoc($query_pesanan)) { ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $p['nama_pembeli']; ?></div>
                                <small class="text-muted">📞 <?php echo $p['no_hp']; ?></small><br>
                                <small class="text-muted">🕒 <?php echo $p['tanggal_pesan']; ?></small>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary"><?php echo nl2br($p['daftar_produk']); ?></div>
                                <small class="text-secondary">📍 <?php echo $p['alamat']; ?></small>
                            </td>
                            <td class="fw-bold text-success">Rp <?php echo number_format($p['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><?php echo $p['status']; ?></span>
                            </td>
                            <td class="text-center">
                                <form action="" method="POST" class="d-inline-block mb-1">
                                    <input type="hidden" name="id_pesanan" value="<?php echo $p['id_pesanan']; ?>">
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="Baru" <?php if($p['status']=='Baru') echo 'selected'; ?>>Baru</option>
                                        <option value="Diproses" <?php if($p['status']=='Diproses') echo 'selected'; ?>>Diproses</option>
                                        <option value="Selesai" <?php if($p['status']=='Selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                    <input type="hidden" name="ubah_status" value="1">
                                </form><br>
                                <a href="chat.php?id_pesanan=<?php echo $p['id_pesanan']; ?>&id_toko=<?php echo $id_toko; ?>" 
                                   class="btn btn-sm btn-dark rounded-pill px-3 mt-1">💬 Chat</a>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>