<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<body class="body-grey">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div class="login-page" id="content">
        <div class="container-cstm">
            <div class="screen">
                <div class="screen__content">
					<div class="col-8 pt-5 mt-4 ps-3">
						<img src="<?= esc(base_url('/img/flavia_logo_rs1.png')); ?>" class="img-fluid float-left" alt="logo" loading="lazy">
					</div>
                    <?php if(session()->getFlashdata('error') != '' || session()->getFlashdata('error') != NULL){ ?>
                        <div class="col-11 mt-4 mx-auto">
                            <div class="alert alert-danger mb-0" role="alert">
                                <?= session()->getFlashdata('error'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(session()->getFlashdata('lockout_message') != '' || session()->getFlashdata('lockout_message') != NULL){ ?>
                        <div class="col-11 mt-4 mx-auto">
                            <div class="alert alert-danger mb-0" role="alert">
                                <?= session()->getFlashdata('lockout_message'); ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form class="login col-10" action="<?= esc(base_url('/login/authentication')); ?>"  method="POST" novalidate>
						<?= csrf_field() ?>
                        <div class="login__field">
                            <i class="login__icon fas fa-user"></i>
                            <input type="text" class="login__input" placeholder="Username" name="username" 
                                required maxlength="30" pattern="[a-zA-Z0-9]+" 
                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" 
                                autocomplete="off">
                        </div>
                        <div class="login__field">
                            <i class="login__icon fas fa-lock"></i>
                            <input type="password" class="login__input" id="password_box" placeholder="Password" 
                                name="password" minlength="6" required 
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="togglePassword()" style="border: none; background: none; cursor: pointer;">
                                <i class="fas fa-eye position-absolute" style="right:77px;bottom:35px;" id="toggleIcon"></i>
                            </button>
                        </div>
                        <button class="button login__submit">
                            <span class="button__text">Log In</span>
                            <i class="button__icon fas fa-chevron-right"></i>
                        </button>
                        <a class="button login__submit mt-2 cstm_link" href="<?= esc(base_url('/registration'))?>">
                            <span class="button__text">DAFTAR</span>
                            <i class="button__icon fas fa-chevron-right"></i>
                        </a>
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
