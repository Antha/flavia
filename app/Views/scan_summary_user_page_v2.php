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
                        <a href="<?php echo esc(base_url()."home"); ?>" class="back-btn">
                            <i class="fa-regular fa-circle-left float-start"></i>
                        </a>
                        <span class="page-navigation-title">
                            REPORT
                        </span>
                    </div>
                </div>
            </div>
            <div class="container greeting-wrapper">
                <div class="greeting mb-4 text-end">
                    <h5 class="font_style_mobile1"><i class="fa-solid fa-user-tie me-2"></i> Welcome, <?= esc(session('username')); ?></h5>
                </div>
            </div>

            <div class="container home-title">
                <div class="row justify-content-center">
                    <div class="col-sm-8 col-10">
                        <div class="home-title-periode text-center position-relative">
                            <h1 class="font_style_mobile2"><?= esc($maxUpdateDate); ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reward-info-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-12 mx-auto">
                            <div class="row justify-content-center">
                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1 mx-auto"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">SCAN</span>
                                        <span class="w-100 d-block menu-item-text3">BYU</span>
                                        <span class="w-100 d-block mt-3 mb-3 menu-item-text1"><?= esc(number_format($resultDataByu)); ?></span>
                                    </div>
                                </div>

                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">SCAN</span>
                                        <span class="w-100 d-block menu-item-text3">PERDANA</span>
                                        <span class="w-100 d-block mt-3 mb-3 menu-item-text1"><?= esc(number_format($resultDataPerdana)); ?></span>
                                    </div>
                                </div>

                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">TOTAL</span>
                                        <span class="w-100 d-block menu-item-text3">SCAN</span>
                                        <span class="w-100 d-block mt-3 mb-3 menu-item-text1"><?= esc(number_format($resultDataTotal)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-wrapper mt-md-5 mt-4">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <?php if (session()->has('errors')): ?>
                                <div class="alert alert-danger">
                                    <?= session('errors') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-2 col-4">
                            <span class="d-inline-block fw-bold" style="font-size: 14px;color: #e0091f;padding-top: 15px;">Report Summary Valid Scan</span>
                        </div>
                    </div>
                </div>
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-8">
                            <form method="post" action="<?php echo esc(base_url('/report/user_report')); ?>" enctype="multipart/form-data">
                                <?php csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-4 pe-2" id="col_periode_data">
                                        <div class="input-group dropdown_input">
                                            <input required type="text" class="monthPicker form-control pull-left txt-input-data" id="periode_data" name="periode_data" value="<?= esc($displayInputDate); ?>" readonly/>
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-2 ps-0">			
                                        <input type="submit" id="btn_submit_periode" name="btn_submit_periode" value="GO" class="submit_btn_datepicker rounded float-start">
                                    </div>

                                    <p class="flashdata_error"><?= session()->getFlashdata('table_not_exists'); ?></p> 
                                    
                                    <div style="clear: both;"></div>
                                </div>
                            </form>
                        </div> 
                        <div class="col-lg-4">
                            <div class="row">
                                <!--<div class="col-9">
                                    <div class="input-group">
                                        <input required type="text" id="searchInput" class="form-control txt-input-data" 
                                            placeholder="Search..." onkeyup="filterTable()" 
                                            style="font-size: 14px;" 
                                            autocomplete="off" 
                                            pattern="[A-Za-z0-9 ]{1,50}" 
                                            oninput="sanitizeInput(this)">
                                        <div class="input-group-addon">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="offset-9 col-3 ps-0">
                                    <button id="exportCsv" class="submit_btn rounded w-100">DOWNLOAD</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-report-wrapper mt-1">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <table class="table table-responsive table-bordered" id="dataTable">
                                <thead>
                                    <tr class="header-top-wrapper">
                                        <th class="text-center">No</th>
                                        <th class="text-center">USERNAME</th>
                                        <th class="text-center">OUTLET NAME</th>
                                        <th class="text-center">DIGIPOS ID</th>
                                        <th class="text-center">SO BYU VALID</th>
                                        <th class="text-center">SO PREPAID VALID</th>
                                        <th class="text-center">SO VALID TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable_body_filter">
                                    <?php $i=1;foreach($resumeScan as $rows){ ?>
                                        <tr>
                                            <td class="text-center"><?= $i; ?></td>
                                            <td><?= esc($rows['username']); ?></td>
                                            <td><?= esc($rows['outlet_name']); ?></td>
                                            <td class="text-center"><?= esc($rows['digipos_id']); ?></td>
                                            <td class="text-center"><?= esc($rows['so_byu_valid']); ?></td>
                                            <td class="text-center"><?= esc($rows['so_perdana_valid']); ?></td>
                                            <td class="text-center"><?= esc($rows['so_total_valid']); ?></td>
                                        </tr>
                                    <?php $i++;} ?>
                                </tbody>
                            </table>
                        </div>
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

        $('#periode_data').datepicker({
            format: "yyyymm",
            startView: 1,
            minViewMode:1,
            autoclose: true,
            todayHighlight: true
        });
        
        $('.table-scroll-bar').width($('#dataTable').outerWidth());

        // Synchronize scrolling
        $('.table-top-scroll').on('scroll', function () {
            $('.table-responsive').scrollLeft($(this).scrollLeft());
        });

        $('.table-responsive').on('scroll', function () {
            $('.table-top-scroll').scrollLeft($(this).scrollLeft());
        });
    });

    function filterTable() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("dataTable_body_filter");
        const rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName("td");
            let match = false;
            
            for (let j = 0; j < cells.length; j++) {
                if (cells[j]) {
                    const textValue = cells[j].textContent || cells[j].innerText;
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        match = true;
                        break;
                    }
                }
            }
            
            rows[i].style.display = match ? "" : "none";
        }
    }

    $('#exportCsv').click(function () {
        function exportTableToCSV(filename) {
            var csv = [];
            var rows = $('#dataTable').find('tr');

            rows.each(function () {
                var row = [];
                $(this).find('th, td').each(function () {
                    // Bungkus isi sel dengan tanda kutip ganda untuk menangani koma dalam sel
                    row.push('"' + $(this).text().trim() + '"');
                });
                csv.push(row.join(','));
            });

            var csvContent = csv.join("\n");
            var blob = new Blob([csvContent], { type: "text/csv" });

            // Deteksi apakah dijalankan di Android atau browser
            if (window.Android && typeof window.Android.downloadCSV === 'function') {
                // Android: Kirim data melalui JavaScriptInterface
                var reader = new FileReader();
                reader.onload = function () {
                    window.Android.downloadCSV(reader.result, filename);
                };
                reader.readAsText(blob);
            } else {
                // Browser: Gunakan mekanisme unduh standar
                var downloadLink = document.createElement('a');
                downloadLink.href = URL.createObjectURL(blob);
                downloadLink.download = filename;
                downloadLink.style.display = 'none';

                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        }

        // Call the function with a file name
        const dateformat = new Date().toISOString().replace(/[-:.TZ]/g, '').slice(0, 14); // Format YYYYMMDDHHMMSS
        const exported_fname = `table_export_${dateformat}.csv`;
        exportTableToCSV(exported_fname);

    });
</script>

<?php $this->endSection() ?>
