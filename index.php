<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowFinder - Temukan Skincare Idealmu</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #ff5e78;
            --secondary: #ffc2c2;
            --bg-gradient: linear-gradient(135deg, #fff5f7 0%, #fff0f5 100%);
            --shadow: 0 20px 40px rgba(255, 94, 120, 0.1);
        }
        
        body { font-family: 'Outfit', sans-serif; background: var(--bg-gradient); color: #2d3436; overflow-x: hidden; }
        
        /* ANIMASI */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-enter { animation: fadeUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .delay-1 { animation-delay: 0.1s; opacity: 0; }

        /* NAVBAR */
        .navbar { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); padding: 20px 0; border-bottom: 1px solid rgba(0,0,0,0.03); }
        .brand-logo { font-weight: 800; font-size: 1.6rem; color: var(--primary); letter-spacing: -0.5px; }
        .nav-link { font-weight: 600; color: #666; transition: 0.3s; margin-left: 25px; }
        .nav-link:hover { color: var(--primary); }

        /* HERO */
        .hero { padding: 80px 20px 140px; text-align: center; }
        .hero h1 { font-weight: 800; font-size: 3.2rem; letter-spacing: -1px; margin-bottom: 15px; color: #2d3436; }
        .hero-badge { display: inline-flex; align-items: center; background: white; padding: 8px 20px; border-radius: 50px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); color: var(--primary); font-weight: 700; font-size: 0.9rem; margin-bottom: 20px; }
        
        /* GLASS CARD */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 2px solid white; border-radius: 40px;
            box-shadow: var(--shadow);
            margin-top: -100px; padding: 50px; position: relative; z-index: 10;
        }

        /* BPOM Banner */
        .bpom-strip {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            border-left: 5px solid #2196f3; border-radius: 10px;
            padding: 20px; display: flex; align-items: center; gap: 15px;
            margin-bottom: 40px; color: #0d47a1;
        }

        /* CARD INPUTS */
        .skin-radio, .concern-checkbox { display: none; }
        
        .selector-card {
            background: white; border: 2px solid transparent; border-radius: 25px;
            padding: 30px 20px; text-align: center; cursor: pointer;
            transition: all 0.4s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            height: 100%; position: relative;
        }
        .selector-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(255, 94, 120, 0.15); }
        
        .icon-circle {
            width: 70px; height: 70px; background: #fff5f7; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px; font-size: 2rem; color: var(--primary);
            transition: 0.3s;
        }

        /* ACTIVE STATE */
        .skin-radio:checked + .selector-card, 
        .concern-checkbox:checked + .selector-card {
            border-color: var(--primary); background: #fffcfd;
        }
        .skin-radio:checked + .selector-card .icon-circle, 
        .concern-checkbox:checked + .selector-card .icon-circle {
            background: var(--primary); color: white; transform: scale(1.1); box-shadow: 0 10px 20px rgba(255, 94, 120, 0.3);
        }

        /* BUTTON MAGIC */
        .btn-magic {
            background: linear-gradient(135deg, #ff5e78 0%, #ff8fa3 100%);
            color: white; padding: 20px 60px; font-weight: 700; border-radius: 100px;
            border: none; font-size: 1.2rem; width: 100%; letter-spacing: 1px;
            box-shadow: 0 20px 40px rgba(255, 94, 120, 0.3);
            transition: 0.4s; position: relative; overflow: hidden;
        }
        .btn-magic:hover { transform: translateY(-5px); box-shadow: 0 25px 50px rgba(255, 94, 120, 0.5); }

        /* LOADING */
        #loadingOverlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);
            z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center;
        }
        .loader-heart {
            font-size: 3rem; color: var(--primary); animation: beat 0.8s infinite alternate; margin-bottom: 20px;
        }
        @keyframes beat { to { transform: scale(1.2); opacity: 0.7; } }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="brand-logo text-decoration-none" href="#">
                <i class="bi bi-stars"></i> GlowFinder<span style="font-weight:300; color:#444;">Beauty</span>
            </a>
        </div>
    </nav>

    <div class="hero">
        <div class="animate-enter">
            <div class="hero-badge"><i class="bi bi-patch-check-fill me-2"></i> Resmi & Terdaftar BPOM</div>
            <h1 class="display-3 fw-bold">Skincare Apa yang<br><span style="color: var(--primary);">Cocok Untukmu?</span></h1>
            <p class="text-muted fs-5">Jawab kuis singkat ini & kami akan carikan produk terbaik<br>yang sesuai dengan kondisi kulitmu.</p>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card animate-enter delay-1">
                    
                    <div class="bpom-strip">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Keamanan Terjamin</h6>
                            <small>Kami hanya merekomendasikan produk yang aman dan legal di Indonesia.</small>
                        </div>
                    </div>

                    <form action="result.php" method="POST" onsubmit="showLoading()">
                        
                        <div class="mb-5 text-center">
                            <h5 class="fw-bold mb-4" style="letter-spacing: 1px; color:#999;">LANGKAH 1: TIPE KULIT KAMU</h5>
                            <div class="row g-3 justify-content-center">
                                <div class="col-6 col-md">
                                    <input type="radio" name="skin_type" value="Berminyak Berjerawat" id="s1" class="skin-radio" required>
                                    <label for="s1" class="selector-card">
                                        <div class="icon-circle"><i class="bi bi-droplet-fill"></i></div>
                                        <h6 class="fw-bold">Berminyak</h6>
                                        <small class="text-muted">Mudah Berjerawat</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md">
                                    <input type="radio" name="skin_type" value="Kering" id="s2" class="skin-radio">
                                    <label for="s2" class="selector-card">
                                        <div class="icon-circle"><i class="bi bi-moisture"></i></div>
                                        <h6 class="fw-bold">Kering</h6>
                                        <small class="text-muted">Terasa Ketarik</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md">
                                    <input type="radio" name="skin_type" value="Sensitif" id="s3" class="skin-radio">
                                    <label for="s3" class="selector-card">
                                        <div class="icon-circle"><i class="bi bi-flower1"></i></div>
                                        <h6 class="fw-bold">Sensitif</h6>
                                        <small class="text-muted">Mudah Merah</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md">
                                    <input type="radio" name="skin_type" value="Kusam Noda Hitam" id="s4" class="skin-radio">
                                    <label for="s4" class="selector-card">
                                        <div class="icon-circle"><i class="bi bi-sun"></i></div>
                                        <h6 class="fw-bold">Kusam</h6>
                                        <small class="text-muted">Kurang Bercahaya</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md">
                                    <input type="radio" name="skin_type" value="Penuaan Dini" id="s5" class="skin-radio">
                                    <label for="s5" class="selector-card">
                                        <div class="icon-circle"><i class="bi bi-hourglass-split"></i></div>
                                        <h6 class="fw-bold">Penuaan</h6>
                                        <small class="text-muted">Garis Halus</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5 text-center">
                            <h5 class="fw-bold mb-4" style="letter-spacing: 1px; color:#999;">LANGKAH 2: KELUHAN SAAT INI</h5>
                            <div class="row g-3 px-lg-5">
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Jerawat Acne" id="c1" class="concern-checkbox">
                                    <label for="c1" class="selector-card py-3">
                                        <h6 class="mb-0">Jerawat Aktif</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Noda Hitam Bekas Jerawat" id="c2" class="concern-checkbox">
                                    <label for="c2" class="selector-card py-3">
                                        <h6 class="mb-0">Bekas Jerawat</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Pori-pori Besar" id="c3" class="concern-checkbox">
                                    <label for="c3" class="selector-card py-3">
                                        <h6 class="mb-0">Pori Besar</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Kemerahan Iritasi" id="c4" class="concern-checkbox">
                                    <label for="c4" class="selector-card py-3">
                                        <h6 class="mb-0">Kemerahan</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Kusam Brightening" id="c5" class="concern-checkbox">
                                    <label for="c5" class="selector-card py-3">
                                        <h6 class="mb-0">Ingin Glowing</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Minyak Sebum" id="c6" class="concern-checkbox">
                                    <label for="c6" class="selector-card py-3">
                                        <h6 class="mb-0">Sangat Berminyak</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Kerutan Garis Halus" id="c7" class="concern-checkbox">
                                    <label for="c7" class="selector-card py-3">
                                        <h6 class="mb-0">Kerutan</h6>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="checkbox" name="concerns[]" value="Kering Dehidrasi" id="c8" class="concern-checkbox">
                                    <label for="c8" class="selector-card py-3">
                                        <h6 class="mb-0">Kulit Kasar</h6>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn-magic">
                                Cari Produk Sekarang <i class="bi bi-search-heart ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="text-center pb-4 text-muted small">
        &copy; 2025 GlowFinder Beauty.
    </footer>

    <div id="loadingOverlay">
        <i class="bi bi-heart-fill loader-heart"></i>
        <h3 class="fw-bold" style="color: #333;">Sedang Mencari...</h3>
        <p class="text-muted">Kami sedang memilihkan produk terbaik untukmu</p>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
    </script>

</body>
</html>