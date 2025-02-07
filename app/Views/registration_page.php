<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<body class="body-grey">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div class="login-page" id="content">
        <div class="container-cstm">
            <div class="screen">
                <div class="screen__content">
                    <div class="d-inline-block text-center w-100 pt-4">
                        <h4 class="mb-0">REGISTRATION</h4>
                    </div>

                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                   
                    <form class="registration col-12 pt-2 pb-2 pe-3 ps-3" action="<?= esc(base_url('/registration/auth')); ?>"  method="POST">
						<?= csrf_field() ?>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="text" class="login__input w-100 ps-3" placeholder="Username"  name="username" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="password" class="login__input w-100 ps-3" id="password_box" placeholder="Password"  name="password" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="password" class="login__input w-100 ps-3" id="confirm_password_box" placeholder="Confirm Password"  name="confirm_password" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="email" class="login__input w-100 ps-3" id="email_box" placeholder="Email"  name="email" required>
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control registration-option-wrapper" id="branch_option" name="branch_option" style="color: #757575;" required>
                                <option value="" selected disabled>Branch</option>
                                <option>DENPASAR</option>
                                <option>FLORES</option>
                                <option>KUPANG</option>
                                <option>MATARAM</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control registration-option-wrapper" id="cluster_option" name="cluster_option" style="color: #757575;" required>
                                <option value="" selected disabled>Cluster</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control registration-option-wrapper" id="city_option" name="city_option" style="color: #757575;" required>
                                <option value="" selected disabled>City</option>
                            </select>
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
</script>

<?php $this->endSection() ?>
