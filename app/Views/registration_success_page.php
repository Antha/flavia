<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<style>
    html, body {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    #content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .home-content {
        flex: 1;
    }

    footer {
        margin-top: auto;
    }
</style>

<body class="body-grey">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div id="content">
        <div class="home-content mt-md-5 mb-md-5 pt-md-3 mt-4 mb-4 pt-2">
            <div class="container home-title">
                <div class="alert alert-success text-center" role="alert">
                    <h4 class="alert-heading">
                        <i class="bi bi-check-circle-fill me-2"></i> Registrasi Berhasil!
                    </h4>
                    <p>Selamat, registrasi Anda telah berhasil. Anda sekarang dapat menggunakan semua fitur yang tersedia.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <?= $this->include('/includes/include_footer'); ?>
    </footer>
</body>

<script>
    $(document).ready(function () {
        $('#security_chckbox').on('change', function () {
            $('#btn_login').prop('disabled', !this.checked).toggleClass('disable-btn', !this.checked);
        });
    });
</script>

<?php $this->endSection() ?>
