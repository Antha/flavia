<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<body class="body-grey">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div class="login-page" id="content">
        <div class="container-cstm">
            <div class="screen_registration">
                <div class="screen__content">
                    <div class="d-inline-block text-center w-100 pt-4">
                        <h4 class="mb-0">REGISTRATION</h4>
                    </div>

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
                   
                    <form class="registration col-12 pt-2 pb-2 pe-3 ps-3" action="<?= esc(base_url('/registration/auth')); ?>"  method="POST">
						<?= csrf_field() ?>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="text" class="login__input w-100 ps-3" placeholder="Username" value="<?= old('username') ?>" name="username" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="password" class="login__input w-100 ps-3" id="password_box" placeholder="Password" value="<?= old('password') ?>" name="password" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="password" class="login__input w-100 ps-3" id="confirm_password_box" placeholder="Confirm Password" value="<?= old('confirm_password') ?>" name="confirm_password" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="email" class="login__input w-100 ps-3" id="email_box" placeholder="Email"  name="email" value="<?= old('email') ?>"  required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="text" class="login__input w-100 ps-3" id="outlate_name" placeholder="Outlet Name"  name="outlet_name" value="<?= old('outlet_name') ?>"  required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="number" class="login__input w-100 ps-3" id="digipos_id" placeholder="Digipos ID"  name="digipos_id" value="<?= old('digipos_id') ?>"  required>
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
                        <button  type="submit" class="button login__submit">
                            <span class="button__text">Register Now</span>
                            <i class="button__icon fas fa-chevron-right"></i>
                        </button>
                    </form>
                </div>
                <div class="screen__background">
                    <span class="screen__background__shape screen__background__shape3"></span>		
                    <span class="screen__background__shape screen__background__shape2"></span>
                    <span class="screen__background__shape screen__background__shape1"></span>
                </div>		
            </div>
        </div>
    </div>
</body>

<script>
    $(document).ready(function () {
        $(function () {
            $('#security_chckbox').on('change', function () {
                $('#btn_login').prop('disabled', !this.checked).toggleClass('disable-btn', !this.checked);
            });
        });

        const clusterOptions = {
            "DENPASAR": ["BALI BARAT", "BALI TENGAH", "BALI TIMUR"],
            "FLORES": ["ENDE SIKKA", "FLORES TIMUR", "MANGGARAI"],
            "KUPANG": ["KUPANG ROTE", "MALAKA TIMTIM BELU", "SUMBA"],
            "MATARAM": ["LOMBOK", "SUMBAWA BARAT", "SUMBAWA TIMUR"]
        };

        $('#branch_option').on('change', function() {
            let branch = $(this).val();
            let clusterDropdown = $('#cluster_option');
            let cityDropdown = $('#city_option');
            
            clusterDropdown.html('<option value="" selected disabled>Cluster</option>');
            cityDropdown.html('<option value="" selected disabled>City</option>');
            
        
            if (clusterOptions[branch]) {
                clusterOptions[branch].forEach(cluster => {
                    clusterDropdown.append(`<option value="${cluster}">${cluster}</option>`);
                });
            }
        
        });

        const cityOptions = {
            'BALI BARAT': ['BULELENG','JEMBRANA','TABANAN'],
            'BALI TENGAH': ['BADUNG','KOTA DENPASAR'],
            'BALI TIMUR': ['BANGLI','GIANYAR','KARANG ASEM','KLUNGKUNG'],
            'ENDE SIKKA': ['ENDE','SIKKA'],
            'FLORES TIMUR': ['ALOR','FLORES TIMUR','LEMBATA'],
            'KUPANG ROTE': ['KOTA KUPANG','KUPANG','ROTE NDAO'],
            'MALAKA TIMTIM BELU': ['BELU','MALAKA','TIMOR TENGAH SELATAN','TIMOR TENGAH UTARA'],
            'MANGGARAI': ['MANGGARAI','MANGGARAI BARAT','MANGGARAI TIMUR','NAGEKEO','NGADA'],
            'SUMBA': ['SABU RAIJUA','SUMBA BARAT','SUMBA BARAT DAYA','SUMBA TENGAH','SUMBA TIMUR'],
            'LOMBOK': ['KOTA MATARAM','LOMBOK BARAT','LOMBOK TENGAH','LOMBOK TIMUR','LOMBOK UTARA'],
            'SUMBAWA BARAT': ['SUMBAWA','SUMBAWA BARAT'],
            'SUMBAWA TIMUR': ['BIMA','DOMPU','KOTA BIMA'],
        };

        $('#cluster_option').on('change', function() {
            let cluster = $(this).val();
            let cityDropdown = $('#city_option');
            
            cityDropdown.html('<option value="" selected disabled>City</option>');
        
            if (cityOptions[cluster]) {
                cityOptions[cluster].forEach(city => {
                    cityDropdown.append(`<option value="${city}">${city}</option>`);
                });
            }
        
        });
    });

    function togglePassword() {
        const passwordField = document.getElementById("password_box");
        console.log(passwordField);
        const toggleIcon = document.getElementById("toggleIcon");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }

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
