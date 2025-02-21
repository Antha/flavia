<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FLAVIA</title>
    <meta name="description" content="FRONTLINER ACTIVITY ON INTEGRATED APLICATION">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- SCRIPT -->
    <script type="text/javascript" src="<?php echo base_url('/script/jquery-3.7.1.min.js') ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('/script/bootstrap.bundle.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('/script/bootstrap-datepicker.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('/script/loader.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!--Font Awesome-->
    <link href="<?php echo base_url('/assets/fontawesome/css/fontawesome.css') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('/assets/fontawesome/css/brands.css') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('/assets/fontawesome/css/solid.css') ?>" rel="stylesheet" />

    <!-- STYLES -->
    <link rel="stylesheet" href="<?php echo base_url('/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('/css/custom.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('/css/datepicker.css') ?>">
</head>

<?= $this->renderSection('content'); ?>

</html>
