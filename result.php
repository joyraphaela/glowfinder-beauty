<?php
// ============================================================
// result.php — Halaman Hasil Rekomendasi GlowFinder Beauty
// Integrasi: koneksi.php + Logic.php (TF-IDF & Cosine Similarity)
// ============================================================

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/Logic.php';

// ── Default state ──────────────────────────────────────────────
$recommendations  = [];
$userSkin         = 'Umum';
$displayConcerns  = 'Perawatan Dasar';
$errorMessage     = '';

// ── Proses form POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $engine      = new RecommendationEngine($conn);
    $userSkin    = isset($_POST['skin_type']) ? trim($_POST['skin_type']) : 'Tidak Spesifik';
    $concernsArr = isset($_POST['concerns']) && is_array($_POST['concerns'])
                   ? $_POST['concerns']
                   : [];

    // Label tampilan untuk concerns
    if (!empty($concernsArr)) {
        $cleanConcerns   = array_map(fn($v) => explode(' ', trim($v))[0], $concernsArr);
        $displayConcerns = implode(', ', $cleanConcerns);
    }

    // Gabungkan semua concerns jadi satu string untuk query
    $userConcernsStr = !empty($concernsArr) ? implode(' ', $concernsArr) : '';

    try {
        $recommendations = $engine->getRecommendations($userSkin, $userConcernsStr);
    } catch (Exception $e) {
        $errorMessage = 'Terjadi kesalahan saat memproses rekomendasi: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi - GlowFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root { --primary: #ff5e78; --bg-soft: #fff5f7; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--bg-soft); color: #333; }

        /* Navbar */
        .navbar { background: white; padding: 15px 0; border-bottom: 1px solid #eee; }
        .brand-logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); }

        /* Header Summary */
        .result-header { background: white; padding: 40px 0; margin-bottom: 50px; border-bottom: 1px solid #f0f0f0; }
        .user-tag { background: #333; color: white; padding: 5px 15px; border-radius: 30px; font-size: 0.85rem; margin-left: 10px; }

        /* PRODUCT CARD */
        .product-card {
            border: none; border-radius: 30px; background: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03); transition: 0.4s;
            height: 100%; display: flex; flex-direction: column; position: relative;
            padding-top: 55px; /* Memberi ruang agar teks tidak menabrak badge di atas */
        }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(255, 94, 120, 0.15); }

        /* Match Badge (Tanpa Gambar, Background disesuaikan) */
        .match-badge {
            position: absolute; top: 15px; left: 15px;
            background: #e8f5e9; 
            padding: 6px 15px; border-radius: 50px; font-weight: 800; color: #2ed573;
            font-size: 0.85rem; z-index: 2;
            display: flex; align-items: center; gap: 5px;
        }

        /* Rank badge top right */
        .rank-badge {
            position: absolute; top: 15px; right: 15px;
            background: var(--primary); color: white;
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.85rem; z-index: 2;
            box-shadow: 0 5px 10px rgba(255,94,120,0.4);
        }

        .card-body { padding: 0 25px 25px 25px; display: flex; flex-direction: column; flex-grow: 1; }
        .brand-name { font-size: 0.8rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .product-name { font-weight: 800; font-size: 1.2rem; color: #2d3436; line-height: 1.3; margin-bottom: 10px; }

        .tags-row { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 15px; }
        .tag-pill { font-size: 0.7rem; background: #e3f2fd; color: #2196f3; padding: 4px 10px; border-radius: 6px; font-weight: 700; }
        .tag-pill.bpom { background: #e8f5e9; color: #2e7d32; }
        .tag-pill.type { background: #f3e5f5; color: #7b1fa2; }

        .desc-text { font-size: 0.9rem; color: #636e72; margin-bottom: 20px; flex-grow: 1;
            display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }

        .footer-card { border-top: 1px solid #f1f2f6; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
        .price { font-weight: 800; font-size: 1.2rem; color: var(--primary); }
        .price-original { font-size: 0.75rem; color: #aaa; text-decoration: line-through; display: block; }

        .btn-view {
            background: #2d3436; color: white; padding: 10px 25px; border-radius: 50px;
            font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.3s;
        }
        .btn-view:hover { background: var(--primary); transform: translateX(5px); color: white; }

        /* Empty state */
        .empty-state { padding: 80px 20px; text-align: center; }

        /* Algorithm info banner */
        .algo-banner {
            background: linear-gradient(135deg, #e3f2fd, #fce4ec);
            border-radius: 16px; padding: 16px 24px; margin-bottom: 40px;
            display: flex; align-items: center; gap: 14px; font-size: 0.85rem; color: #555;
        }

        /* Cosine score badge */
        .cosine-badge { font-size: 0.72rem; color: #b2bec3; display: block; margin-top: 4px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="brand-logo text-decoration-none" href="index.php">
                <i class="bi bi-stars"></i> GlowFinder<span style="font-weight:300; color:#444;">Beauty</span>
            </a>
            <a href="index.php" class="btn btn-sm btn-outline-dark rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Cari Ulang
            </a>
        </div>
    </nav>

    <div class="result-header text-center">
        <div class="container">
            <h6 class="text-uppercase text-muted fw-bold mb-2">Hasil Analisis</h6>
            <h2 class="fw-bold display-6 mb-3">Rekomendasi Spesial Untukmu</h2>
            <div>
                <span class="user-tag"><i class="bi bi-person"></i> <?= htmlspecialchars($userSkin) ?></span>
                <span class="user-tag bg-secondary"><i class="bi bi-bullseye"></i> <?= htmlspecialchars($displayConcerns) ?></span>
            </div>
        </div>
    </div>

    <div class="container pb-5">

        <?php if ($errorMessage): ?>
        <div class="alert alert-danger rounded-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($recommendations)): ?>

       <div class="algo-banner">
            <i class="bi bi-stars fs-4 text-primary"></i>
            <div>
                <strong>Personalized Match</strong> &mdash; 
                Kami telah menganalisis jenis kulitmu dan menyeleksi formulasi bahan aktif terbaik yang paling ampuh untuk mengatasi keluhanmu saat ini.
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($recommendations as $index => $item): ?>
                <?php
                    // Format harga
                    $priceDisplay    = 'Rp ' . number_format((float)$item['discount_price'], 0, ',', '.');
                    $priceOriginal   = '';
                    if (!empty($item['normal_price']) &&
                        (float)$item['normal_price'] > (float)$item['discount_price']) {
                        $priceOriginal = 'Rp ' . number_format((float)$item['normal_price'], 0, ',', '.');
                    }

                    // Karena tanpa gambar ruang teks lebih lega, batas deskripsi dinaikkan jadi 200 karakter
                    $desc = htmlspecialchars(trim($item['description_product'] ?? ''));
                    if (mb_strlen($desc) > 200) {
                        $desc = mb_substr($desc, 0, 200) . '…';
                    }

                    // Tipe produk label
                    $typeLabel = ucfirst($item['product_type'] ?? '');
                    $rank      = $index + 1;
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="product-card">

                        <div class="match-badge">
                            <i class="bi bi-check-circle-fill"></i>
                            <?= $item['score'] ?>% Cocok
                        </div>
                        <div class="rank-badge">#<?= $rank ?></div>

                        <div class="card-body">
                            <div class="brand-name"><?= htmlspecialchars($item['brand'] ?? '') ?></div>
                            <div class="product-name"><?= htmlspecialchars($item['product_name'] ?? '') ?></div>

                            <div class="tags-row">
                                <?php if (!empty($item['bpom_id']) && $item['bpom_id'] !== 'NA'): ?>
                                <span class="tag-pill bpom">
                                    <i class="bi bi-shield-check"></i> BPOM
                                </span>
                                <?php endif; ?>
                                <span class="tag-pill type"><?= $typeLabel ?></span>
                                <?php if (!empty($item['rating']) && (float)$item['rating'] > 0): ?>
                                <span class="tag-pill">
                                    <i class="bi bi-star-fill"></i> <?= number_format((float)$item['rating'], 1) ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <p class="desc-text"><?= $desc ?></p>

                            <span class="cosine-badge">
                                <i class="bi bi-activity"></i>
                                Cosine Score: <?= $item['cosine_raw'] ?>
                            </span>

                            <div class="footer-card">
                                <div>
                                    <?php if ($priceOriginal): ?>
                                    <span class="price-original"><?= $priceOriginal ?></span>
                                    <?php endif; ?>
                                    <span class="price"><?= $priceDisplay ?></span>
                                </div>
                                <?php if (!empty($item['product_url'])): ?>
                                <a href="<?= htmlspecialchars($item['product_url']) ?>"
                                   target="_blank" rel="noopener noreferrer"
                                   class="btn-view">
                                    Lihat Produk <i class="bi bi-arrow-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div></div></div><?php endforeach; ?>
        </div><?php else: ?>

        <div class="empty-state">
            <div class="display-1 text-muted opacity-25 mb-3"><i class="bi bi-search"></i></div>
            <?php if (!$errorMessage): ?>
            <h4 class="text-muted fw-bold">Maaf, belum ada produk yang cocok.</h4>
            <p class="text-secondary">Coba ubah kombinasi jenis kulit atau keluhanmu.</p>
            <?php endif; ?>
            <a href="index.php" class="btn btn-lg rounded-pill px-5 mt-3"
               style="background: var(--primary); color: white; font-weight: 700;">
                <i class="bi bi-arrow-left me-2"></i> Coba Lagi
            </a>
        </div>

        <?php endif; ?>

    </div><footer class="text-center pb-4 pt-2 text-muted small">
        &copy; 2025 GlowFinder Beauty &mdash;
    </footer>

</body>
</html>