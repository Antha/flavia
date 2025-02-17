<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<style>
     .modal {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
</style>

<div class="modal" id="modal" style="display: none;"> 
    <div class="modal-content">
        <h4>Data Outlet Adjustment</h4>
        <?php if(session()->has('error_image')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert"">
                <?= session('error_image') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->has('errors')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill"></i> Oops! Terjadi kesalahan:</strong>
                <ul class="mt-2 mb-0">
                    <?php foreach (session('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <form  action="/registration/update" method="post" enctype="multipart/form-data">
            <input type="hidden" value="<?php echo session()->get("user_id")?>" name="id" />
            <div class="form-group">
                <label for="outlate_name">Outlet Name</label>
                <input type="text" id="outlet_name" name="outlet_name" required>
            </div>
            <div class="form-group">
                <label for="link_aja">Link Aja</label>
                <div style="display: flex;">
                    <span style="padding: 7px;
                        background: gray;
                        color: white;
                        font-size: 10pt;">+62</span>
                        <input type="text" id="link_aja" name="link_aja" required>
                </div>
            </div>
            <div class="form-group">
                <label for="digipos_id">Digipos ID</label>
                <input type="text" id="digipos_id" name="digipos_id" required>
            </div>
            <div class="form-group mt-4">
                <input type="hidden" name="imageData" id="imageData">
                <h6 class="card-title">Take Identify Card Photo</h6>
                <div class="d-flex justify-content-center align-items-center">
                    <video id="video" autoplay class="border rounded" style="max-width: 100%; height: auto;"></video>
                </div>
                <button id="capture" type="button" class="btn btn-primary submit_btn mt-2 mb-3 float-end" style="font-size: 12px;">Capture</button>
                <canvas id="canvas" class="mt-3 border rounded" style="max-width: 100%; display: none;"></canvas>
            </div>
            <button type="submit" class="btn btn-secondary">Submit</button>
        </form>
    </div>
</div>

<body class="body-grey">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div id="content">
        <?= $this->include('/includes/include_top_navbar'); ?>

        <div class="home-content mt-md-5 mb-md-5 pt-md-3 mt-4 mb-4 pt-2">
            <div class="container greeting-wrapper">
                <div class="greeting mb-4 text-end">
                    <h5 class="font_style_mobile1"><i class="fa-solid fa-user-tie me-2"></i> Welcome, <?= esc(session('username')); ?></h5>
                </div>
            </div>

                
            <?php if(session()->has('success_message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success_message') ?>
                </div>
            <?php endif; ?>


            <div class="container home-title">
                <div class="row justify-content-center">
                    <div class="col-sm-8 col-10">
                        <div class="home-title-periode text-center position-relative">
                            <h1 class="font_style_mobile2"><?= esc($maxUpdateDate); ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container point-display-home mt-md-4 mb-md-5 mt-3 mb-4">
                <div class="row justify-content-center">
                    <div class="col-3 text-center position-relative menu-item ps-1 pe-1 ps-md-5 pe-md-5">
                        <div class="menu-item-wrapper">
                            <span class="position-absolute menu-item-fa1 mx-auto"><i class="fa-solid fa-qrcode"></i></span>
                            <span class="w-100 d-block menu-item-text1 mt-4">SCAN</span>
                            <span class="w-100 d-block menu-item-text1">BYU</span>
                            <span class="w-100 d-block mt-3 mb-3 menu-item-text2"><?= esc(number_format($resultDataByu)); ?></span>
                        </div>
                    </div>
                    <div class="col-3 text-center position-relative menu-item ps-1 pe-1 ps-md-5 pe-md-5">
                        <div class="menu-item-wrapper">
                            <span class="position-absolute menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                            <span class="w-100 d-block menu-item-text1 mt-4">SCAN</span>
                            <span class="w-100 d-block menu-item-text1">PERDANA</span>
                            <span class="w-100 d-block mt-3 mb-3 menu-item-text2"><?= esc(number_format($resultDataPerdana)); ?></span>
                        </div>
                    </div>
                    <div class="col-3 text-center position-relative menu-item ps-1 pe-1 ps-md-5 pe-md-5">
                        <div class="menu-item-wrapper">
                            <span class="position-absolute menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                            <span class="w-100 d-block menu-item-text1 mt-4">TOTAL</span>
                            <span class="w-100 d-block menu-item-text1">SCAN</span>
                            <span class="w-100 d-block mt-3 mb-3 menu-item-text2"><?= esc(number_format($resultDataTotal)); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container menu-bottom-wrapper pt-md-5 pt-4">
                <div class="row justify-content-center">
                    <div class="col-3 text-center">
                        <a class="item-menu-bottom1 d-inline-block" href="<?= esc(base_url('/qris?card_type=byu')); ?>">
                            <div class="img-wrapper rounded-circle mx-auto">
                                <img src="<?= esc('/img/icon_scan_white.png'); ?>" class="img-fluid" loading="lazy" alt="scan byu">
                            </div>
                            <span class="img-title">Increase ByU Scan Point</span>
                        </a>
                    </div>
                    <div class="col-3 text-center">
                        <a class="item-menu-bottom1 d-inline-block" href="<?= esc(base_url('/qris?card_type=perdana')); ?>">
                            <div class="img-wrapper rounded-circle mx-auto">
                                <img src="<?= esc('/img/icon_scan_white.png'); ?>" class="img-fluid" loading="lazy" alt="scan perdana">
                            </div>
                            <span class="img-title">Increase Perdana Scan Point</span>
                        </a>
                    </div>
                    <div class="col-3 text-center">
                        <a class="item-menu-bottom1 d-inline-block" href="<?= esc(base_url(session("user_level") == 'admin' ? '/report/admin_report' : '/report/user_report')); ?>">
                            <div class="img-wrapper rounded-circle mx-auto">
                                <img src="<?= esc('/img/icon_reward_white.png'); ?>" class="img-fluid" loading="lazy" alt="scan byu">
                            </div>
                            <span class="img-title">Check Point & Reward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->include('/includes/include_footer'); ?>
    </div>
</body>

<script>
    $(document).ready(function () {
        $(function () {
            $('#security_chckbox').on('change', function () {
                $('#btn_login').prop('disabled', !this.checked).toggleClass('disable-btn', !this.checked);
            });
        }); 
    });

    const modal = document.getElementById("modal");

    <?php if (!session()->get("outlet_name") || !session()->get("link_aja") || !session()->get("digipos_id") || !session()->get("idcard") || session()->get("idcard") == 0): ?>
        modal.style.display = "flex";
    <?php endif?>

    // Hapus event listener yang menutup modal saat klik di luar
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            event.stopPropagation(); // Men
        }
    });

    document.getElementById('link_aja').addEventListener('input', function() {
        let inputValue = this.value;
        if (inputValue.startsWith('0')) {
            // Remove the leading 0
            this.value = inputValue.substring(1);
        }
    });

    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureButton = document.getElementById('capture');
    const imageDataInput = document.getElementById('imageData');

    // Akses kamera
    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            video.srcObject = stream;
        })
        .catch((err) => {
            console.error('Error accessing camera: ', err);
        });

    // Capture gambar
    captureButton.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Tampilkan canvas
        canvas.style.display = 'block';

        // Ambil data gambar sebagai base64
        const imageData = canvas.toDataURL('image/png');
        imageDataInput.value = imageData;
        //saveButton.disabled = false;

        const scrollHeight = document.body.scrollHeight;
        
        const scrollStep = 500;
       
        const button = document.querySelector(".button.login__submit");
        if (button) {
            button.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    })
</script>

<?php $this->endSection() ?>
