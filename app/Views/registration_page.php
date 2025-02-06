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
                   
                    <form class="registration col-12 pt-2 pb-2 pe-3 ps-3" action="<?= esc(base_url('/registration/auth')); ?>"  method="POST">
						<?= csrf_field() ?>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="text" class="login__input w-100 ps-3" placeholder="Username"  name="username" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="password" class="login__input w-100 ps-3" id="password_box" placeholder="Password"  name="password" required>
                        </div>
                        <div class="pt-2 pb-0 position-relative">
                            <input type="password" class="login__input w-100 ps-3" id="password_box" placeholder="Confirm Password"  name="password" required>
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control registration-option-wrapper" id="exampleFormControlSelect1" style="color: #757575;">
                                <option value="" selected disabled>Branch</option>
                                <option>DENPASAR</option>
                                <option>FLORES</option>
                                <option>KUPANG</option>
                                <option>MATARAM</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control registration-option-wrapper" id="exampleFormControlSelect1" style="color: #757575;">
                                <option value="" selected disabled>Cluster</option>
                                <option>DENPASAR</option>
                                <option>FLORES</option>
                                <option>KUPANG</option>
                                <option>MATARAM</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <select class="form-control registration-option-wrapper" id="exampleFormControlSelect1" style="color: #757575;">
                                <option value="" selected disabled>City</option>
                                <option>DENPASAR</option>
                                <option>FLORES</option>
                                <option>KUPANG</option>
                                <option>MATARAM</option>
                            </select>
                        </div>
                        <button class="button login__submit">
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
