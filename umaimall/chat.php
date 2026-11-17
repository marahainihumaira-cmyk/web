<?php
include 'koneksi.php';

$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0;
$id_toko    = isset($_GET['id_toko']) ? intval($_GET['id_toko']) : 1;

// Ambil data pesanan & pembeli
$q_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan");
$pesanan = mysqli_fetch_assoc($q_pesanan);

if (!$pesanan) {
    die("<h3 style='text-align:center; margin-top:50px;'>Data pesanan tidak ditemukan!</h3>");
}

// Proses kirim pesan chat
if (isset($_POST['kirim_pesan'])) {
    $pengirim = mysqli_real_escape_string($koneksi, $_POST['pengirim']); // 'customer' atau 'owner'
    $pesan    = mysqli_real_escape_string($koneksi, $_POST['pesan']);
    
    if (!empty($pesan)) {
        mysqli_query($koneksi, "INSERT INTO chat_pesanan (id_pesanan, pengirim, pesan) VALUES ($id_pesanan, '$pengirim', '$pesan')");
        header("Location: chat.php?id_pesanan=$id_pesanan&id_toko=$id_toko");
        exit();
    }
}

// Ambil riwayat chat
$q_chat = mysqli_query($koneksi, "SELECT * FROM chat_pesanan WHERE id_pesanan = $id_pesanan ORDER BY waktu_pesan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Pesanan #<?php echo $id_pesanan; ?> - UmaiMall</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .chat-box { height: 400px; overflow-y: scroll; background: #fff; border-radius: 15px; padding: 20px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.03); }
        .chat-bubble { max-width: 75%; padding: 12px 16px; border-radius: 15px; margin-bottom: 15px; font-size: 14px; }
        .chat-owner { background: #e3f2fd; margin-left: auto; text-align: right; border-bottom-right-radius: 2px; }
        .chat-customer { background: #f1f0f0; margin-right: auto; border-bottom-left-radius: 2px; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items: center">
                        <div>
                            <h5 class="mb-0 fw-bold">💬 Ruang Diskusi Pesanan #<?php echo $id_pesanan; ?></h5>
                            <small class="text-light opacity-75">Pemesan: <?php echo $pesanan['nama_pembeli']; ?></small>
                        </div>
                        <a href="owner.php?id=<?php echo $id_toko; ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">← Kembali ke Panel Owner</a>
                    </div>
                    
                    <div class="card-body bg-light">
                        <!-- Kotak Riwayat Chat -->
                        <div class="chat-box mb-3">
                            <?php 
                            if(mysqli_num_rows($q_chat) > 0) {
                                while($c = mysqli_fetch_assoc($q_chat)) {
                                    $is_owner = ($c['pengirim'] == 'owner');
                            ?>
                            <div class="chat-bubble <?php echo $is_owner ? 'chat-owner' : 'chat-customer'; ?>">
                                <div class="fw-bold small text-muted mb-1">
                                    <?php echo $is_owner ? '👑 Owner Toko' : '🛒 Customer (' . $pesanan['nama_pembeli'] . ')'; ?> 
                                    <span class="ms-2 fw-normal" style="font-size: 11px;"><?php echo $c['waktu_pesan']; ?></span>
                                </div>
                                <div class="text-dark"><?php echo nl2br($c['pesan']); ?></div>
                            </div>
                            <?php 
                                }
                            } else {
                                echo '<div class="text-center text-muted my-auto py-5">Belum ada percakapan. Mulai kirim pesan di bawah!</div>';
                            }
                            ?>
                        </div>

                        <!-- Form Kirim Pesan -->
                        <form action="" method="POST">
                            <div class="input-group mb-2">
                                <select name="pengirim" class="form-select form-select-sm" style="max-width: 150px;" required>
                                    <option value="owner">Kirim sebagai Owner</option>
                                    <option value="customer">Kirim sebagai Customer</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <textarea name="pesan" class="form-control" rows="2" placeholder="Tulis pesan atau konfirmasi pesanan di sini..." required></textarea>
                                <button type="submit" name="kirim_pesan" class="btn btn-dark px-4 fw-bold">Kirim 🚀</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>