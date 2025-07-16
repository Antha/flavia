<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<body class="bg-white">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div id="content">
        <?= $this->include('/includes/include_top_navbar_area'); ?>

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
                            <form id="filterForm" method="post" action="<?php echo esc(base_url('/report/admin_report')); ?>" enctype="multipart/form-data">
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
                    </div>
                </div>
            </div>
            
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <span style="font-weight: bold;font-style: italic">Last Scan Date : <?php echo $maxUpdateDate; ?></span>
                    </div>
                </div>
            </div>
            <div class="table-report-wrapper mt-2">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <span style="font-weight: bold;font-style: italic">Summary Per Area</span>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="table-wrapper-scroll-y table-scroll-y">
                                <div class="table-responsive">
                                    <table class="table table-responsive table-bordered" id="dataSummary">
                                        <thead>
                                            <tr class="header-top-wrapper">
                                                <th class="align-middle" style="width: 180px;">REGIONAL</th>
                                                <th class="align-middle">FL REGISTER</th>
                                                <th class="align-middle">FL SCAN</th>
                                                <th class="align-middle">% FL SCAN TO FL REGISTER</th>
                                                <th class="align-middle">FL AKTIF(>1 SCAN VALID)</th>
                                                <th class="align-middle">% FL SCAN TO FL AKTIF</th>
                                                <th class="align-middle">MSISDN SCAN</th>
                                                <th class="align-middle">MSISDN AKTIF SO</th>
                                                <th class="align-middle">RENEWAL</th>
                                                <th class="align-middle">RASIO RENEWAL TO SO</th>
                                                <th class="align-middle">REVENUE RENEWAL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dataSummary_body_filter">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="search-section mt-md-5 mt-4">
                            <div class="container">
                                <div class="row">
                                    <div class="offset-lg-8 col-lg-4 col-12 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <input required type="text" id="searchInput" class="form-control txt-input-data" placeholder="Search..." onkeyup="filterTable()" style="font-size: 14px;" autocomplete="off">
                                                    <div class="input-group-addon">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3 p-0">
                                                <button id="exportCsv" class="submit_btn rounded w-100">DOWNLOAD</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <span style="font-weight: bold;font-style: italic">Summary Per FL</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <div class="table-wrapper-scroll-y table-scroll-y">
                                <div class="table-responsive">
                                    <table class="table table-responsive table-bordered" id="dataTable">
                                        <thead>
                                            <tr class="header-top-wrapper">
                                                <th class="align-middle">FL ID</th>
                                                <th class="align-middle">NAMA</th>
                                                <th class="align-middle">OUTLET NAME</th>
                                                <th class="align-middle">CLUSTER</th>
                                                <th class="align-middle">MSISDN SCAN</th>
                                                <th class="align-middle">MSISDN AKTIF SO</th>
                                                <th class="align-middle">RENEWAL</th>
                                                <th class="align-middle">REVENUE RENEWAL</th>
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
            function numberFormat(number) {
                const num = Number(number);
                if (isNaN(num)) return '0';
                return num.toLocaleString('en-US');
            }


            function loadData(periode = "") {
                // Disable tombol saat loading
                $("#btn_submit").prop("disabled", true);
                $("#exportCsv").prop("disabled", true);

                //tampilkan keterangan loading di dalam table
                $("#dataSummary_body_filter").html('<tr><td colspan="15" class="text-center text-danger">Loading....</td></tr>');
                $("#dataTable_body_filter").html('<tr><td colspan="15" class="text-center text-danger">Loading....</td></tr>');

                if (periode !== "" && !/^\d{6}$/.test(periode)) { 
                    Swal.fire({
                        icon: "error",
                        title: "Periode Tidak Valid!",
                        text: "Periode harus berupa angka 6 digit (YYYYMM).",
                    });
                    //$("#dataSummary_body_filter").html(""); // Kosongkan tabel jika input salah
                    $("#dataTable_body_filter").html(""); // Kosongkan tabel jika input salah
                    //enable tombol kembali
                    $("#btn_submit").prop("disabled", false);
                    $("#exportCsv").prop("disabled", false);
                    return;
                }

                $.ajax({
                    url: "<?= base_url('/report/admin_report_area') ?>", // Sesuaikan dengan URL controller
                    type: "POST",
                    data: { periode_data: periode }, // Kirim periode ke server
                    dataType: "json",
                    success: function(response) {
                        $("#dataTable_body_filter").html();
                        $("#dataSummary_body_filter").html();
                        if (response.error) {
                            Swal.fire({
                                icon: "warning",
                                title: "Data Tidak Ditemukan",
                                text: response.error,
                            });
                            $("#dataTable_body_filter").html('<tr><td colspan="15" class="text-center text-danger">Data tidak ditemukan</td></tr>');
                            $("#dataSummary_body_filter").html('<tr><td colspan="15" class="text-center text-danger">Data tidak ditemukan</td></tr>');
                            return;
                        }

                        let html = "";
                        $.each(response.resumeScan, function(index, row) {
                            html += `<tr>
                                <td class="text-start">${row.user_id}</td>
                                <td class="text-start">${row.fl_name}</td>
                                <td class="text-start">${row.outlet_name}</td>
                                <td class="text-start">${row.cluster}</td>
                                <td class="text-end">${numberFormat(row.msisdn_scan || 0)}</td>
                                <td class="text-end">${numberFormat(row.msisdn_aktif_so || 0)}</td>
                                <td class="text-end">${numberFormat(row.renewal || 0)}</td>
                                <td class="text-end">${numberFormat(row.revenue_renewal || 0)}</td>
                            </tr>`;
                        });

                        let htmlSummary = "";
                        $.each(response.resumeScanPerRegional, function(index, rowRegional) {
                            if(rowRegional.regional == 'BALI NUSRA'){
                                htmlSummary += `<tr class="fw-bold" style="background-color: #D9D9D9">
                                    <td class="text-start">${rowRegional.regional}</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_register || 0)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_scan || 0)}</td>
                                    <td class="text-center">${rowRegional.fl_scan_to_register}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_aktif || 0)}</td>
                                    <td class="text-center">${rowRegional.fl_aktif_to_register}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.msisdn_scan || 0)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.msisdn_aktif_so)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.renewal || 0)}</td>
                                    <td class="text-center">${rowRegional.renewal_to_so}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.rev_renewal || 0)}</td>
                                </tr>`; 
                            }
                        });
                        $.each(response.resumeScanPerCluster, function(index, rowCluster) {
                            if(rowCluster.cluster == 'BALI BARAT' || rowCluster.cluster == 'BALI TIMUR' || rowCluster.cluster == 'LOMBOK'){
                                htmlSummary += `<tr>
                                    <td class="text-start">${rowCluster.cluster}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_register || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_scan || 0)}</td>
                                    <td class="text-center">${rowCluster.fl_scan_to_register}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_aktif || 0)}</td>
                                    <td class="text-center">${rowCluster.fl_aktif_to_register}</td>
                                    <td class="text-center">${numberFormat(rowCluster.msisdn_scan || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.msisdn_aktif_so || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.renewal)}</td>
                                    <td class="text-center">${rowCluster.renewal_to_so}</td>
                                    <td class="text-center">${numberFormat(rowCluster.rev_renewal || 0)}</td>
                                </tr>`; 
                            }
                        });
                        $.each(response.resumeScanPerRegional, function(index, rowRegional) {
                            if(rowRegional.regional == 'JATENG-DIY'){
                                htmlSummary += `<tr class="fw-bold" style="background-color: #D9D9D9">
                                    <td class="text-start">${rowRegional.regional}</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_register || 0)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_scan || 0)}</td>
                                    <td class="text-center">${rowRegional.fl_scan_to_register}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_aktif || 0)}</td>
                                    <td class="text-center">${rowRegional.fl_aktif_to_register}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.msisdn_scan || 0)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.msisdn_aktif_so)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.renewal || 0)}</td>
                                    <td class="text-center">${rowRegional.renewal_to_so}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.rev_renewal || 0)}</td>
                                </tr>`; 
                            }
                        });
                        $.each(response.resumeScanPerCluster, function(index, rowCluster) {
                            if(rowCluster.regional == 'JATENG-DIY'){
                                htmlSummary += `<tr>
                                    <td class="text-start">${rowCluster.cluster}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_register || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_scan || 0)}</td>
                                    <td class="text-center">${rowCluster.fl_scan_to_register}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_aktif || 0)}</td>
                                    <td class="text-center">${rowCluster.fl_aktif_to_register}</td>
                                    <td class="text-center">${numberFormat(rowCluster.msisdn_scan || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.msisdn_aktif_so || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.renewal)}</td>
                                    <td class="text-center">${rowCluster.renewal_to_so}</td>
                                    <td class="text-center">${numberFormat(rowCluster.rev_renewal || 0)}</td>
                                </tr>`; 
                            }
                        });
                        $.each(response.resumeScanPerRegional, function(index, rowRegional) {
                            if(rowRegional.regional == 'JATIM'){
                                htmlSummary += `<tr class="fw-bold" style="background-color: #D9D9D9">
                                    <td class="text-start">${rowRegional.regional}</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_register || 0)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_scan || 0)}</td>
                                    <td class="text-center">${rowRegional.fl_scan_to_register}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.fl_aktif || 0)}</td>
                                    <td class="text-center">${rowRegional.fl_aktif_to_register}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.msisdn_scan || 0)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.msisdn_aktif_so)}</td>
                                    <td class="text-center">${numberFormat(rowRegional.renewal || 0)}</td>
                                    <td class="text-center">${rowRegional.renewal_to_so}%</td>
                                    <td class="text-center">${numberFormat(rowRegional.rev_renewal || 0)}</td>
                                </tr>`; 
                            }
                        });
                        $.each(response.resumeScanPerCluster, function(index, rowCluster) {
                            if(rowCluster.regional == 'JATIM'){
                                htmlSummary += `<tr>
                                    <td class="text-start">${rowCluster.cluster}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_register || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_scan || 0)}</td>
                                    <td class="text-center">${rowCluster.fl_scan_to_register}</td>
                                    <td class="text-center">${numberFormat(rowCluster.fl_aktif || 0)}</td>
                                    <td class="text-center">${rowCluster.fl_aktif_to_register}</td>
                                    <td class="text-center">${numberFormat(rowCluster.msisdn_scan || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.msisdn_aktif_so || 0)}</td>
                                    <td class="text-center">${numberFormat(rowCluster.renewal)}</td>
                                    <td class="text-center">${rowCluster.renewal_to_so}</td>
                                    <td class="text-center">${numberFormat(rowCluster.rev_renewal || 0)}</td>
                                </tr>`; 
                            }
                        });
                        $.each(response.resumeScanAllRegional, function(index, rowArea) {
                        
                            htmlSummary += `<tr class="fw-bold" style="background-color: #D9D9D9">
                                <td class="text-start">${rowArea.areas}</td>
                                <td class="text-center">${numberFormat(rowArea.fl_register || 0)}</td>
                                <td class="text-center">${numberFormat(rowArea.fl_scan || 0)}</td>
                                <td class="text-center">${rowArea.fl_scan_to_register}%</td>
                                <td class="text-center">${numberFormat(rowArea.fl_aktif || 0)}</td>
                                <td class="text-center">${rowArea.fl_aktif_to_register}%</td>
                                <td class="text-center">${numberFormat(rowArea.msisdn_scan || 0)}</td>
                                <td class="text-center">${numberFormat(rowArea.msisdn_aktif_so)}</td>
                                <td class="text-center">${numberFormat(rowArea.renewal || 0)}</td>
                                <td class="text-center">${rowArea.renewal_to_so}%</td>
                                <td class="text-center">${numberFormat(rowArea.rev_renewal || 0)}</td>
                            </tr>`; 
                            
                        });
                        
                        
                        $("#dataTable_body_filter").html(html);
                        $("#dataSummary_body_filter").html(htmlSummary);

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
            const exported_fname = `scan_summary_${dateformat}.csv`;
            exportTableToCSV(exported_fname);

        });

        function nf0(num) {
            return Number(num).toLocaleString('id-ID'); // or your preferred locale
        }
    </script>
</body>

<?php $this->endSection() ?>
