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
                            <h1 id="maxUpdateDate" class="font_style_mobile2"></h1>
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
                                        <span id="resultDataByu" class="w-100 d-block mt-3 mb-3 menu-item-text1"></span>
                                    </div>
                                </div>

                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">SCAN</span>
                                        <span class="w-100 d-block menu-item-text3">PERDANA</span>
                                        <span id="resultDataPerdana" class="w-100 d-block mt-3 mb-3 menu-item-text1"></span>
                                    </div>
                                </div>

                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">TOTAL</span>
                                        <span class="w-100 d-block menu-item-text3">SCAN</span>
                                        <span id="resultDataTotal" class="w-100 d-block mt-3 mb-3 menu-item-text1"></span>
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
                        <div class="col-lg-4 col-md-2 col-10">
                            <span class="d-inline-block fw-bold" style="font-size: 14px;color: #e0091f;padding-top: 15px;">Report Summary Valid Scan</span>
                        </div>
                    </div>
                </div>
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-lg-8 col-6">
                            <form method="post" action="<?php echo esc(base_url('/report/user_report')); ?>" enctype="multipart/form-data">
                                <?php csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-10 pe-lg-2 pe-0" id="col_periode_data">
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
                        <div class="col-lg-4 col-6">
                            <div class="row">
                                <div class="offset-lg-9 col-lg-3 offset-6 col-6 ps-0">
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
                            <div class="table-wrapper-scroll-y table-scroll-y">
                                <div class="table-top-scroll">
                                    <div class="table-scroll-bar"></div>
                                </div>
                                <div class="table-responsive">     
                                    <table class="table table-responsive table-bordered" id="dataTable">
                                        <thead>
                                            <tr class="header-top-wrapper">
                                                <th class="text-center">No</th>
                                                <th class="text-center">FL NAME</th>
                                                <th class="text-center">OUTLET NAME</th>
                                                <th class="text-center">DIGIPOS ID</th>
                                                <th class="text-center">SO BYU VALID</th>
                                                <th class="text-center">SO PREPAID VALID</th>
                                                <th class="text-center">SO VALID TOTAL</th>
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
        </div>
        <?= $this->include('/includes/include_footer'); ?>
    </div>
    <script>
        $(document).ready(function () {
            function loadData(periode = "") {
                // Disable tombol saat loading
                $("#btn_submit_periode").prop("disabled", true);
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
                    url: "<?= base_url('/report/user_report_realtime') ?>", // Sesuaikan dengan URL controller
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
                            $("#maxUpdateDate").text(response.maxUpdateDate ?? 'No Data');
                            $("#resultDataByu").text(response.resultDataByu ?? 0);
                            $("#resultDataPerdana").text(response.resultDataPerdana ?? 0);
                            $("#resultDataTotal").text(response.resultDataTotal ?? 0);
                            $("#poinDataTotal").text(response.poinDataTotal ?? 0);
                            return;
                        }

                        let html = "";
                        let no = 1;
                        $.each(response.resumeScan, function(index, row) {
                            html += `<tr>
                                <td class="text-center">${no}</td>
                                <td>${row.fl_name}</td>
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
                        $("#maxUpdateDate").text(response.maxUpdateDate ?? 'No Data');
                        $("#resultDataByu").text(response.resultDataByu ?? 0);
                        $("#resultDataPerdana").text(response.resultDataPerdana ?? 0);
                        $("#resultDataTotal").text(response.resultDataTotal ?? 0);
                        $("#poinDataTotal").text(response.poinDataTotal ?? 0);
                    },
                    error: function(xhr, status, error) {
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

            // Refresh Data Saat Tombol Submit Periode Diklik
            $("#btn_submit_periode").click(function() {
                let periode = $("#periode_data").val().trim();
                loadData(periode);
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
</body>
<?php $this->endSection() ?>
