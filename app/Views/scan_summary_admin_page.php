<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<body class="bg-white">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div id="content">
        <?= $this->include('/includes/include_top_navbar'); ?>

        <div class="report-summary-content mt-md-5 mb-md-5 pt-md-3 mt-4 mb-4 pt-2">
            <div class="container page-navigation-wrapper mb-2">
                <div class="row">
                    <div class="page-navigation">
                        <a href="<?= esc(base_url()."home"); ?>" class="back-btn">
                            <i class="fa-regular fa-circle-left float-start"></i>
                        </a>
                        <span class="page-navigation-title">REPORT</span>
                    </div>
                </div>
            </div>
            <div class="container greeting-wrapper">
                <div class="greeting mb-4 text-end">
                    <h5 class="font_style_mobile1"><i class="fa-solid fa-user-tie me-2"></i> Welcome, <?= esc(session('username')); ?></h5>
                </div>
            </div>

            <div class="filter-wrapper mt-md-5 mt-4">
                <div class="container">
                    <div class="row">
                        <div class="col-8">
                            <form id="filterForm" method="post" action="<?php echo esc(base_url('/report/admin_report')); ?>" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-4 pe-2">
                                        <div class="input-group dropdown_input">
                                            <input required type="text" class="monthPicker form-control pull-left txt-input-data" id="periode_data" name="periode_data" value="<?= esc($displayInputDate); ?>"/>
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-2 ps-0">			
                                        <button type="submit" id="btn_submit" class="submit_btn_datepicker rounded float-start">GO</button>
                                    </div>
                                    <p id="errorMessage" class="text-danger"></p> 
                                    <div style="clear: both;"></div>
                                </div>
                            </form>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-9">
                                    <div class="input-group">
                                        <input required type="text" id="searchInput" class="form-control txt-input-data" placeholder="Search..." onkeyup="filterTable()" style="font-size: 14px;" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 ps-0">
                                    <button id="exportCsv" class="submit_btn rounded w-100">DOWNLOAD</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-report-wrapper mt-3">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="table-wrapper-scroll-y table-scroll-y">
                                <div class="table-top-scroll">
                                    <div class="table-scroll-bar"></div>
                                </div>
                                <table class="table table-responsive table-bordered" id="dataTable">
                                    <thead>
                                        <tr class="header-top-wrapper">
                                            <th>No</th>
                                            <th>FL NAME</th>
                                            <th>OUTLET NAME</th>
                                            <th>DIGIPOS ID</th>
                                            <th>SO BYU VALID</th>
                                            <th>SO PREPAID VALID</th>
                                            <th>SO TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataTable_body_filter">
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->include('/includes/include_footer'); ?>
    </div>

    <link rel="stylesheet" href="<?php echo base_url('/css/datepicker.css') ?>">
    <script type="text/javascript" src="<?php echo base_url('/script/bootstrap-datepicker.js') ?>"></script>
    <script>
        $(document).ready(function () {
            function loadData(periode = "") {
                // Disable tombol saat loading
                $("#btn_submit").prop("disabled", true);
                $("#exportCsv").prop("disabled", true);

                //tampilkan keterangan loading di dalam table
                $("#dataTable_body_filter").html('<tr><td colspan="7" class="text-center text-danger">Loading....</td></tr>');

                if (periode !== "" && !/^\d{6}$/.test(periode)) { 
                    Swal.fire({
                        icon: "error",
                        title: "Periode Tidak Valid!",
                        text: "Periode harus berupa angka 6 digit (YYYYMM).",
                    });
                    $("#dataTable_body_filter").html(""); // Kosongkan tabel jika input salah
                    //enable tombol kembali
                    $("#btn_submit_periode").prop("disabled", false);
                    $("#exportCsv").prop("disabled", false);
                    return;
                }

                $.ajax({
                    url: "<?= base_url('/report/admin_report') ?>", // Sesuaikan dengan URL controller
                    type: "POST",
                    data: { periode_data: periode }, // Kirim periode ke server
                    dataType: "json",
                    success: function(response) {
                        $("#dataTable_body_filter").html();
                        if (response.error) {
                            Swal.fire({
                                icon: "warning",
                                title: "Data Tidak Ditemukan",
                                text: response.error,
                            });
                            $("#dataTable_body_filter").html('<tr><td colspan="7" class="text-center text-danger">Data tidak ditemukan</td></tr>');
                            return;
                        }

                        let html = "";
                        let no = 1;
                        $.each(response.resumeScan, function(index, row) {
                            html += `<tr>
                                <td class="text-center">${no}</td>
                                <td>${row.username}</td>
                                <td>${row.outlet_name}</td>
                                <td class="text-center">${row.digipos_id}</td>
                                <td class="text-center">${row.so_byu_valid}</td>
                                <td class="text-center">${row.so_perdana_valid}</td>
                                <td class="text-center">${row.so_total_valid}</td>
                            </tr>`;
                            no++;
                        });

                        $("#dataTable_body_filter").html(html);

                        // Update informasi lainnya di halaman
                        $("#maxUpdateDate").text(response.maxUpdateDate);
                        $("#resultDataByu").text(response.resultDataByu);
                        $("#resultDataPerdana").text(response.resultDataPerdana);
                        $("#resultDataTotal").text(response.resultDataTotal);
                        $("#poinDataTotal").text(response.poinDataTotal);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText); // Debugging di console
                        Swal.fire({
                            icon: "error",
                            title: "Gagal Mengambil Data!",
                            text: "Terjadi kesalahan saat mengambil data dari server.",
                        });
                        $("#dataTable_body_filter").html('<tr><td colspan="7" class="text-center text-danger">Gagal Mengambil Data!</td></tr>');
                    },
                    complete: function() {
                        // Enable tombol setelah selesai (baik sukses maupun error)
                        $("#btn_submit_periode").prop("disabled", false);
                        $("#exportCsv").prop("disabled", false);
                    }
                });
            }

            // Load data saat pertama kali halaman dimuat
            loadData(<?= esc($displayInputDate); ?>);

        });
    </script>
</body>

<?php $this->endSection() ?>
