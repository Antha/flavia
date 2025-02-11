<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

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
            <div class="container home-title">
                <div class="row justify-content-center">
                    <div class="col-sm-8 col-10">
                        <div class="home-title-periode text-center position-relative">
                            <h1 class="font_style_mobile2">JANUARY 2025</h1>
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
                            <span class="w-100 d-block mt-3 mb-3 menu-item-text2">500</span>
                        </div>
                    </div>
                    <div class="col-3 text-center position-relative menu-item ps-1 pe-1 ps-md-5 pe-md-5">
                        <div class="menu-item-wrapper">
                            <span class="position-absolute menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                            <span class="w-100 d-block menu-item-text1 mt-4">SCAN</span>
                            <span class="w-100 d-block menu-item-text1">PERDANA</span>
                            <span class="w-100 d-block mt-3 mb-3 menu-item-text2">500</span>
                        </div>
                    </div>
                    <div class="col-3 text-center position-relative menu-item ps-1 pe-1 ps-md-5 pe-md-5">
                        <div class="menu-item-wrapper">
                            <span class="position-absolute menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                            <span class="w-100 d-block menu-item-text1 mt-4">TOTAL</span>
                            <span class="w-100 d-block menu-item-text1">SCAN</span>
                            <span class="w-100 d-block mt-3 mb-3 menu-item-text2">1000</span>
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
</script>

<?php $this->endSection() ?>
