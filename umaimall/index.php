<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umai Mart - Pilih Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .mall-header {
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white; padding: 45px 0; text-align: center; border-radius: 0 0 35px 35px; margin-bottom: 40px;
        }
        .toko-card { border: none; border-radius: 20px; overflow: hidden; transition: transform 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: white; }
        .toko-card:hover { transform: translateY(-8px); }
        .toko-img { height: 200px; object-fit: cover; }
    </style>
</head>
<body>

    <div class="mall-header">
        <h1 class="fw-bold">🛒 Umai Mart Virtual Store</h1>
        <p class="lead mb-3">Pilih cabang toko favoritmu di bawah ini!</p>
        <a href="tambah_toko.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">➕ Buka Toko Baru</a>
    </div>

    <div class="container mb-5">
        <div class="row">
            <?php
            $query = mysqli_query($koneksi, "SELECT * FROM toko");
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card toko-card h-100">
                    <!-- Foto Toko -->
                    <img src="gambar/<?php echo $row['foto_toko']; ?>" class="card-img-top toko-img" alt="<?php echo $row['nama_toko']; ?>" onerror="this.src='https://via.placeholder.com/400x200?text=Umai+Store'">
                    
                    <div class="card-body d-flex flex-column p-4">
                        <h4 class="card-title fw-bold" style="color: <?php echo $row['tema_warna']; ?>;">
                            <?php echo $row['nama_toko']; ?>
                        </h4>
                        <p class="card-text text-muted flex-grow-1">
                            <?php echo $row['deskripsi']; ?>
                        </p>
                        
                        <div class="d-flex gap-2 mt-3">
                            <a href="toko.php?id=<?php echo $row['id_toko']; ?>" 
                               class="btn text-white flex-grow-1 rounded-pill fw-bold" 
                               style="background-color: <?php echo $row['tema_warna']; ?>;">
                                Kunjungi Toko 🚀
                            </a>
                            <a href="owner.php?id=<?php echo $row['id_toko']; ?>" 
                               class="btn btn-outline-dark rounded-pill px-3 fw-bold" title="Panel Owner">
                                ⚙️ Owner
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo '<div class="col-12 text-center py-5">
                        <h4 class="text-muted">Belum ada toko yang terdaftar di Umai Mart.</h4>
                        <a href="tambah_toko.php" class="btn btn-danger rounded-pill mt-2">Buat Toko Pertama Sekarang</a>
                      </div>';
            }
            ?>
        </div>
    </div>

</body>
</html>