<?php
include 'koneksi.php';

$id_toko = isset($_GET['id']) ? intval($_GET['id']) : 1;
$kategori_pilih = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';

$query_toko = mysqli_query($koneksi, "SELECT * FROM toko WHERE id_toko = $id_toko");
$toko = mysqli_fetch_assoc($query_toko);

if (!$toko) {
    die("<h3 style='text-align:center; margin-top:50px;'>Toko tidak ditemukan!</h3>");
}

$pesan_sukses = "";
if (isset($_POST['checkout'])) {
    $nama_pembeli = mysqli_real_escape_string($koneksi, $_POST['nama_pembeli']);
    $no_hp        = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat       = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $produk_dibeli = $_POST['produk'] ?? [];
    $varian_pilih  = $_POST['varian'] ?? [];
    $jumlah_beli   = $_POST['jumlah'] ?? [];
    
    $daftar_pesanan_str = "";
    $total_harga = 0;
    
    foreach ($produk_dibeli as $id_produk) {
        if (isset($jumlah_beli[$id_produk]) && intval($jumlah_beli[$id_produk]) > 0) {
            $jml = intval($jumlah_beli[$id_produk]);
            $var = mysqli_real_escape_string($koneksi, $varian_pilih[$id_produk] ?? '-');
            
            $q_prod = mysqli_query($koneksi, "SELECT * FROM umaimart WHERE no = $id_produk");
            $p = mysqli_fetch_assoc($q_prod);
            
            $subtotal = $p['harga'] * $jml;
            $total_harga += $subtotal;
            
            $daftar_pesanan_str .= "- " . $p['nama_produk'] . " (Varian: " . $var . ") x " . $jml . " = Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        }
    }
    
    if ($total_harga > 0 && !empty($nama_pembeli)) {
        $query_insert = "INSERT INTO pesanan (id_toko, nama_pembeli, no_hp, alamat, daftar_produk, total_harga, status) 
                         VALUES ($id_toko, '$nama_pembeli', '$no_hp', '$alamat', '$daftar_pesanan_str', $total_harga, 'Baru')";
        
        if (mysqli_query($koneksi, $query_insert)) {
            $pesan_sukses = "Pesanan berhasil dibuat! Silakan tunggu konfirmasi owner.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $toko['nama_toko']; ?> - Umai Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .store-header {
            background: linear-gradient(135deg, <?php echo $toko['tema_warna']; ?>, #222);
            color: white; padding: 40px 0; border-radius: 0 0 30px 30px; margin-bottom: 30px;
        }
        .card-custom { border: none; border-radius: 15px; transition: transform 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.05); background: #fff; }
        .card-custom:hover { transform: translateY(-5px); }
        .cat-img, .prod-img { height: 160px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; }
        .btn-theme { background-color: <?php echo $toko['tema_warna']; ?>; color: white; border: none; }
        .btn-theme:hover { opacity: 0.9; color: white; }
    </style>
</head>
<body>

    <div class="store-header text-center">
        <div class="container">
            <span class="badge bg-white text-dark mb-2 px-3 py-1 rounded-pill fw-bold">Umai Mart Store</span>
            <h1 class="fw-bold"><?php echo $toko['nama_toko']; ?></h1>
            <p class="lead mb-0"><?php echo $toko['deskripsi']; ?></p>
            <a href="index.php" class="btn btn-light btn-sm rounded-pill mt-3 px-4 fw-bold">← Kembali ke Umai Mart Utama</a>
        </div>
    </div>

    <div class="container mb-5">
        <?php if (!empty($pesan_sukses)) { ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <strong>Yeay! 🎉</strong> <?php echo $pesan_sukses; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <!-- TAHAP 1: PILIH KATEGORI (MUNCUL NAMA KATEGORI & FOTO KATEGORI) -->
        <?php if (empty($kategori_pilih)) { ?>
            <h3 class="fw-bold mb-4 text-secondary">📂 Pilih Kategori Menu</h3>
            <div class="row">
                <?php
                $q_kat = mysqli_query($koneksi, "SELECT kategori, gambar FROM umaimart WHERE id_toko = $id_toko GROUP BY kategori");
                if(mysqli_num_rows($q_kat) > 0) {
                    while($kat = mysqli_fetch_assoc($q_kat)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card card-custom h-100">
                        <img src="gambar/<?php echo $kat['gambar']; ?>" class="card-img-top cat-img" alt="<?php echo $kat['kategori']; ?>" onerror="this.src='https://via.placeholder.com/300x160?text=Umai+Category'">
                        <div class="card-body text-center p-4 d-flex flex-column">
                            <h4 class="fw-bold text-dark mb-3"><?php echo $kat['kategori']; ?></h4>
                            <a href="toko.php?id=<?php echo $id_toko; ?>&kategori=<?php echo urlencode($kat['kategori']); ?>" 
                               class="btn btn-theme w-100 rounded-pill mt-auto fw-bold">
                                Lihat Menu 🚀
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo '<div class="col-12 text-center py-5 text-muted">Belum ada menu atau kategori di toko ini. Silakan tambah lewat Panel Owner.</div>';
                }
                ?>
            </div>

        <!-- TAHAP 2: PILIH MENU, FOTO, VARIAN, HARGA, JUMLAH, & KERANJANG -->
        <?php } else { ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-secondary">🍽️ Kategori: <?php echo htmlspecialchars($kategori_pilih); ?></h3>
                <a href="toko.php?id=<?php echo $id_toko; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">← Kembali ke Kategori</a>
            </div>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="row">
                            <?php 
                            $q_prod = mysqli_query($koneksi, "SELECT * FROM umaimart WHERE id_toko = $id_toko AND kategori = '$kategori_pilih'");
                            if(mysqli_num_rows($q_prod) > 0) {
                                while($prod = mysqli_fetch_assoc($q_prod)) {
                                    $arr_varian = explode(',', $prod['varian_rasa']);
                            ?>
                            <div class="col-md-6 mb-4">
                                <div class="card card-custom h-100">
                                    <img src="gambar/<?php echo $prod['gambar']; ?>" class="card-img-top prod-img" alt="<?php echo $prod['nama_produk']; ?>" onerror="this.src='https://via.placeholder.com/300x160?text=Umai+Product'">
                                    
                                    <div class="card-body p-3 d-flex flex-column">
                                        <h5 class="fw-bold text-dark mb-1"><?php echo $prod['nama_produk']; ?></h5>
                                        <h6 class="text-success fw-bold mb-2">Rp <?php echo number_format($prod['harga'], 0, ',', '.'); ?></h6>
                                        
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Varian Rasa:</label>
                                            <select name="varian[<?php echo $prod['no']; ?>]" class="form-select form-select-sm">
                                                <?php foreach($arr_varian as $v) { $v = trim($v); ?>
                                                    <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="mb-2 mt-auto">
                                            <label class="form-label small fw-semibold mb-1">Jumlah Beli:</label>
                                            <input type="number" name="jumlah[<?php echo $prod['no']; ?>]" value="0" min="0" max="<?php echo $prod['stok']; ?>" class="form-control form-control-sm">
                                            <input type="hidden" name="produk[]" value="<?php echo $prod['no']; ?>">
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">Stok: <?php echo $prod['stok']; ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                }
                            } else {
                                echo '<div class="col-12 text-center py-5 text-muted">Belum ada menu di kategori ini.</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Keranjang & Form Checkout -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top bg-white" style="top: 20px;">
                            <h4 class="fw-bold mb-3 text-secondary">🛒 Keranjang & Pengiriman</h4>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nama Lengkap</label>
                                <input type="text" name="nama_pembeli" class="form-control" required placeholder="Nama kamu">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nomor WhatsApp</label>
                                <input type="text" name="no_hp" class="form-control" required placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="3" required placeholder="Jalan / Patokan..."></textarea>
                            </div>
                            <button type="submit" name="checkout" class="btn btn-theme w-100 py-2 fw-bold rounded-pill shadow-sm">
                                Checkout Pesanan Sekarang 🚀
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>

</body>
</html>