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
                        <div class="col-lg-8 col-12">
                            <form id="filterForm" method="post" action="<?php echo esc(base_url('/report_np/admin_report')); ?>" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-6 pe-2">
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
                        <div class="col-lg-4 col-12">
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
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <span style="font-weight: bold;font-style: italic">Data dimulai dari tanggal 2025-04-21</span>
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
                                <div class="table-responsive">
                                    <table class="table table-responsive table-bordered" id="dataTable">
                                        <thead>
                                            <tr class="header-top-wrapper">
                                                <th rowspan = '2' class="align-middle">No</th>
                                                <th rowspan = '2' class="align-middle">FL NAME</th>
                                                <th rowspan = '2' class="align-middle">OUTLET NAME</th>
                                                <th rowspan = '2' class="align-middle">DIGIPOS ID</th>
                                                <th colspan = '3'>SO VALID</th>
                                                <th colspan = '8'>RENEWAL</th>
                                            </tr>
                                            <tr class="header-top-wrapper">
                                                <th>BYU</th>
                                                <th>PREPAID</th>
                                                <th>TOTAL</th>
                                                <th>AKUISISI</th>
                                                <th>BONUS</th>
                                                <th>BTL</th>
                                                <th>CORE</th>
                                                <th>ORBIT</th>
                                                <th>OTHERS</th>
                                                <th>VF</th>
                                                <th>TOTAL</th>
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

    <link rel="stylesheet" href="<?php echo base_url('/css/datepicker.css') ?>">
    <script type="text/javascript" src="<?php echo base_url('/script/bootstrap-datepicker.js') ?>"></script>
    <script>
        $(document).ready(function () {
            function loadData(periode = "") {
                // Disable tombol saat loading
                $("#btn_submit").prop("disabled", true);
                $("#exportCsv").prop("disabled", true);

                //tampilkan keterangan loading di dalam table
                $("#dataTable_body_filter").html('<tr><td colspan="15" class="text-center text-danger">Loading....</td></tr>');

                if (periode !== "" && !/^\d{6}$/.test(periode)) { 
                    Swal.fire({
                        icon: "error",
                        title: "Periode Tidak Valid!",
                        text: "Periode harus berupa angka 6 digit (YYYYMM).",
                    });
                    $("#dataTable_body_filter").html(""); // Kosongkan tabel jika input salah
                    //enable tombol kembali
                    $("#btn_submit").prop("disabled", false);
                    $("#exportCsv").prop("disabled", false);
                    return;
                }

                $.ajax({
                    url: "<?= base_url('/report_np/admin_report') ?>", // Sesuaikan dengan URL controller
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
                            $("#dataTable_body_filter").html('<tr><td colspan="15" class="text-center text-danger">Data tidak ditemukan</td></tr>');
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
                                <td class="text-center">${row.so_akuisisi}</td>
                                <td class="text-center">${row.so_bonus}</td>
                                <td class="text-center">${row.so_btl}</td>
                                <td class="text-center">${row.so_core}</td>
                                <td class="text-center">${row.so_orbit}</td>
                                <td class="text-center">${row.so_others}</td>
                                <td class="text-center">${row.so_vf}</td>
                                <td class="text-center">${row.so_pt_total}</td>
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
                        $("#btn_submit").prop("disabled", false);
                        $("#exportCsv").prop("disabled", false);
                    }
                });
            }

            // Load data saat pertama kali halaman dimuat
            loadData(<?= esc($displayInputDate); ?>);

            // Refresh Data Saat Tombol Submit Periode Diklik
            $("#btn_submit").click(function() {
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

            for (let i = 0; i < rows.length; i++) {
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

        function nf0(num) {
            return Number(num).toLocaleString('id-ID'); // or your preferred locale
        }
    </script>
</body>

<?php $this->endSection() ?>
