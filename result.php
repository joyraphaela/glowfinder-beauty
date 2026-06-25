<?php
// ============================================================
// result.php — Halaman Hasil Rekomendasi GlowFinder Beauty
// Integrasi Sempurna: Fitur Tag Lengkap + Teks Banner Custom Baru
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

// ── HELPER FUNCTION: Explainable AI (Penerjemah Kandungan) ─────
function generateSmartExplanation($ingredients_list, $concerns_string) {
    if (empty($ingredients_list) || empty($concerns_string)) return null;
    
    $ing = strtolower($ingredients_list);
    $con = strtolower($concerns_string);
    
    $activeFound = [];
    $targetIssue = "masalah kulitmu";

    // Deteksi Bahan Aktif Populer
    if (strpos($ing, 'niacinamide') !== false) $activeFound[] = 'Niacinamide';
    if (strpos($ing, 'salicylic acid') !== false || strpos($ing, 'bha') !== false) $activeFound[] = 'Salicylic Acid';
    if (strpos($ing, 'centella') !== false || strpos($ing, 'cica') !== false) $activeFound[] = 'Centella Asiatica';
    if (strpos($ing, 'hyaluronic') !== false) $activeFound[] = 'Hyaluronic Acid';
    if (strpos($ing, 'retinol') !== false) $activeFound[] = 'Retinol';
    if (strpos($ing, 'vitamin c') !== false || strpos($ing, 'ascorbic acid') !== false) $activeFound[] = 'Vitamin C';
    if (strpos($ing, 'ceramide') !== false) $activeFound[] = 'Ceramide';
    if (strpos($ing, 'glycolic acid') !== false || strpos($ing, 'aha') !== false) $activeFound[] = 'AHA';

    // Sesuaikan Target dengan Keluhan Utama
    if (strpos($con, 'jerawat') !== false || strpos($con, 'acne') !== false) $targetIssue = 'meredakan jerawat & mencegah breakout';
    elseif (strpos($con, 'kusam') !== false || strpos($con, 'noda') !== false) $targetIssue = 'mencerahkan kulit kusam & noda hitam';
    elseif (strpos($con, 'pori') !== false) $targetIssue = 'membersihkan & menyamarkan pori';
    elseif (strpos($con, 'kemerahan') !== false || strpos($con, 'sensitif') !== false) $targetIssue = 'menenangkan kemerahan & iritasi';
    elseif (strpos($con, 'kering') !== false || strpos($con, 'dehidrasi') !== false) $targetIssue = 'mengunci kelembapan mendalam';
    elseif (strpos($con, 'penuaan') !== false || strpos($con, 'kerutan') !== false) $targetIssue = 'menyamarkan garis halus (anti-aging)';
    elseif (strpos($con, 'minyak') !== false) $targetIssue = 'mengontrol minyak & sebum berlebih';

    // Rangkai Kalimat Penjelasan
    if (!empty($activeFound)) {
        $topActives = array_slice($activeFound, 0, 2); 
        $activesText = implode(' & ', $topActives);
        return "Sistem mendeteksi kandungan <strong>{$activesText}</strong> yang terbukti secara ilmiah ampuh untuk <strong>{$targetIssue}</strong>.";
    }
    return "Diformulasikan secara khusus untuk menyeimbangkan dan memperbaiki kondisi kulitmu saat ini."; 
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

        .navbar { background: white; padding: 15px 0; border-bottom: 1px solid #eee; }
        .brand-logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); cursor: pointer; }

        .result-header { background: white; padding: 40px 0; margin-bottom: 50px; border-bottom: 1px solid #f0f0f0; }
        .user-tag { background: #333; color: white; padding: 5px 15px; border-radius: 30px; font-size: 0.85rem; margin-left: 10px; }

        .product-card {
            border: none; border-radius: 25px; background: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03); transition: 0.4s;
            height: 100%; display: flex; flex-direction: column; position: relative;
            padding-top: 55px; 
        }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(255, 94, 120, 0.15); }

        .match-badge {
            position: absolute; top: 15px; left: 15px;
            background: #e8f5e9; padding: 6px 15px; border-radius: 50px; font-weight: 800; color: #2ed573;
            font-size: 0.85rem; z-index: 2; display: flex; align-items: center; gap: 5px;
        }
        .rank-badge {
            position: absolute; top: 15px; right: 15px;
            background: var(--primary); color: white; width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;
            box-shadow: 0 5px 10px rgba(255,94,120,0.4);
        }

        /* Explainable AI (XAI) Box */
        .xai-box {
            background: #fff9fa; border-left: 4px solid var(--primary);
            padding: 12px 15px; border-radius: 0 8px 8px 0; font-size: 0.85rem; color: #555;
            margin-bottom: 15px; line-height: 1.5;
        }
        .xai-box strong { color: var(--primary); }

        .card-body { padding: 0 25px 25px 25px; display: flex; flex-direction: column; flex-grow: 1; }
        .brand-name { font-size: 0.8rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .product-name { font-weight: 800; font-size: 1.2rem; color: #2d3436; line-height: 1.3; margin-bottom: 12px; }
        
        /* Tags Row Style (Rating, BPOM, Tipe) */
        .tags-row { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 15px; }
        .tag-pill { font-size: 0.7rem; background: #e8ecef; color: #6c757d; padding: 4px 10px; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .tag-pill.bpom { background: #e8f5e9; color: #2e7d32; }
        .tag-pill.type { background: #f3e5f5; color: #7b1fa2; }
        .tag-pill.rating-pill { background: #fffde7; color: #f57f17; }

        .desc-text { font-size: 0.9rem; color: #636e72; margin-bottom: 20px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }

        /* Price Styling */
        .price-section { margin-top: auto; margin-bottom: 15px; }
        .price-normal { text-decoration: line-through; color: #b2bec3; font-size: 0.9rem; font-weight: 500; }
        .price-discount-badge { background: #ff7675; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 800; margin-left: 5px; }
        .price-final { font-weight: 800; font-size: 1.3rem; color: var(--primary); display: block; line-height: 1.2; margin-top: 2px; }
        .price-savings { color: #00b894; font-size: 0.8rem; font-weight: 700; display: block; margin-top: 3px; }

        .footer-card { border-top: 1px solid #f1f2f6; padding-top: 15px; text-align: right; }
        .btn-view { background: #2d3436; color: white; padding: 10px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.3s; display: inline-block;}
        .btn-view:hover { background: var(--primary); transform: translateX(3px); color: white; }

        .dev-mode-data { 
            display: none; background: #2d3436; color: #00ff00; padding: 10px; 
            border-radius: 8px; font-family: monospace; font-size: 0.75rem; 
            margin-bottom: 15px; border: 1px solid #00ff00; text-align: left;
        }

        .algo-banner { background: linear-gradient(135deg, #e3f2fd, #fce4ec); border-radius: 16px; padding: 16px 24px; margin-bottom: 40px; display: flex; align-items: center; gap: 14px; font-size: 0.85rem; color: #555; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="brand-logo text-decoration-none" id="secretLogo">
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
                <strong>Ini dia match sempurnamu!</strong> &mdash; 
                Kita udah cariin ingredients yang paling jago buat hempas masalah kulitmu.
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($recommendations as $index => $item): ?>
                <?php
                    // Kalkulasi Harga Diskon & Hemat
                    $normalPrice   = isset($item['normal_price']) ? (float)$item['normal_price'] : 0;
                    $discountPrice = isset($item['discount_price']) ? (float)$item['discount_price'] : 0;
                    
                    if ($discountPrice <= 0) $discountPrice = $normalPrice; 
                    
                    $hasDiscount = ($normalPrice > $discountPrice);
                    $savings     = $normalPrice - $discountPrice;
                    $discountPct = $normalPrice > 0 ? round(($savings / $normalPrice) * 100) : 0;

                    $priceFinalDisplay  = 'Rp ' . number_format($discountPrice, 0, ',', '.');
                    $priceNormalDisplay = 'Rp ' . number_format($normalPrice, 0, ',', '.');
                    $savingsDisplay     = 'Rp ' . number_format($savings, 0, ',', '.');

                    // Limit Deskripsi
                    $desc = htmlspecialchars(trim($item['description_product'] ?? ''));
                    if (mb_strlen($desc) > 220) {
                        $desc = mb_substr($desc, 0, 220) . '…';
                    }
                    
                    $rank = $index + 1;
                    $typeLabel = ucfirst($item['product_type'] ?? '');

                    // Fitur Explainable AI
                    $explanation = generateSmartExplanation($item['ingredients_list'], $userConcernsStr);
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="product-card">

                        <div class="match-badge">
                            <i class="bi bi-check-circle-fill"></i> <?= $item['score'] ?>% Cocok
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
                                <span class="tag-pill rating-pill">
                                    <i class="bi bi-star-fill"></i> <?= number_format((float)$item['rating'], 1) ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <?php if($explanation): ?>
                                <div class="xai-box">
                                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> <?= $explanation ?>
                                </div>
                            <?php endif; ?>

                            <p class="desc-text"><?= $desc ?></p>

                            <div class="dev-mode-data">
                                [DEV CONSOLE]<br>
                                Algorithm : TF-IDF & Cosine<br>
                                Raw Score : <strong><?= $item['cosine_raw'] ?></strong><br>
                                Matrix ID : PRD-<?= $item['id'] ?>
                            </div>

                            <div class="price-section">
                                <?php if ($hasDiscount): ?>
                                    <div>
                                        <span class="price-normal"><?= $priceNormalDisplay ?></span>
                                        <span class="price-discount-badge">-<?= $discountPct ?>%</span>
                                    </div>
                                <?php endif; ?>
                                
                                <span class="price-final"><?= $priceFinalDisplay ?></span>
                                
                                <?php if ($hasDiscount): ?>
                                    <span class="price-savings"><i class="bi bi-tag-fill me-1"></i>Hemat <?= $savingsDisplay ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="footer-card">
                                <?php if (!empty($item['product_url'])): ?>
                                <a href="<?= htmlspecialchars($item['product_url']) ?>" target="_blank" class="btn-view">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <div class="text-center" style="padding: 80px 20px;">
            <div class="display-1 text-muted opacity-25 mb-3"><i class="bi bi-search"></i></div>
            <h4 class="text-muted fw-bold">Maaf, belum ada produk yang cocok.</h4>
            <a href="index.php" class="btn btn-lg rounded-pill px-5 mt-3" style="background: var(--primary); color: white;">
                <i class="bi bi-arrow-left me-2"></i> Coba Lagi
            </a>
        </div>
        <?php endif; ?>

    </div>

    <footer class="text-center pb-4 pt-2 text-muted small">
        &copy; 2025 GlowFinder Beauty &mdash;
    </footer>

    <script>
        let clickCount = 0;
        document.getElementById('secretLogo').addEventListener('click', function(e) {
            e.preventDefault();
            clickCount++;
            if(clickCount === 3) {
                const devElements = document.querySelectorAll('.dev-mode-data');
                devElements.forEach(el => el.style.display = 'block');
                alert("🔓 DEVELOPER MODE TERBUKA!\n\nSkor mentah Cosine Similarity kini diaktifkan untuk membantu proses validasi data pengujian sistem.");
                clickCount = 0;
            }
        });
    </script>

</body>
</html>
