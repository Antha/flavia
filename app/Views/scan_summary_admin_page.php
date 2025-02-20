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
                            <form id="filterForm">
                                <?= csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-4 pe-2">
                                        <div class="input-group dropdown_input">
                                            <input required type="text" class="monthPicker form-control pull-left txt-input-data" id="periode_data" name="periode_data" />
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
                                        <tr>
                                            <td colspan="7" class="text-center">Loading data...</td>
                                        </tr>
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
        $(document).ready(function() {
            function sanitizeInput(input) {
                input.value = input.value.replace(/[^A-Za-z0-9 ]/g, ''); // Remove special characters
            }

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
            function fetchData(periode = null) {
                $("#errorMessage").text("");
                $("#dataTable_body_filter").html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: "<?= base_url('/report/admin_report') ?>",
                    type: "POST",
                    data: { periode_data: periode },
                    dataType: "json",
                    success: function(response) {
                        if (response.displayInputDate) {
                            $("#periode_data").val(response.displayInputDate); // Update input periode
                        }

                        if (response.resumeScan.length > 0) {
                            updateTable(response.resumeScan);
                        } else {
                            $("#dataTable_body_filter").html('<tr><td colspan="7" class="text-center">No data available</td></tr>');
                        }
                    },
                    error: function() {
                        $("#errorMessage").text("Failed to load data. Please try again.");
                        $("#dataTable_body_filter").html('<tr><td colspan="7" class="text-center">No data available</td></tr>');
                    }
                });
            }

            function updateTable(data) {
                let html = "";
                $.each(data, function(index, row) {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${row.fl_name ? row.fl_name : '-'}</td>
                            <td>${row.outlet_name ? row.outlet_name : '-'}</td>
                            <td>${row.digipos_id ? row.digipos_id : '-'}</td>
                            <td>${row.so_byu_valid ? row.so_byu_valid : 0}</td>
                            <td>${row.so_perdana_valid ? row.so_perdana_valid : 0}</td>
                            <td>${row.so_total ? row.so_total : 0}</td>
                        </tr>`;
                });
                $("#dataTable_body_filter").html(html);
            }

            // Fungsi untuk export CSV
            function exportToCSV(data) {
                if (data.length === 0) {
                    alert("No data available to export.");
                    return;
                }

                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "No,FL NAME,OUTLET NAME,DIGIPOS ID,SO BYU VALID,SO PREPAID VALID,SO TOTAL\n";

                data.forEach((row, index) => {
                    let rowData = [
                        index + 1,
                        row.fl_name ? row.fl_name : '-',
                        row.outlet_name ? row.outlet_name : '-',
                        row.digipos_id ? row.digipos_id : '-',
                        row.so_byu_valid ? row.so_byu_valid : 0,
                        row.so_perdana_valid ? row.so_perdana_valid : 0,
                        row.so_total ? row.so_total : 0
                    ].join(",");
                    csvContent += rowData + "\n";
                });

                let encodedUri = encodeURI(csvContent);
                let link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "admin_report.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Jalankan fetch data pertama kali saat halaman dimuat
            fetchData();

            // Tangani event form submit
            $("#filterForm").submit(function(event) {
                event.preventDefault();
                let periode = $("#periode_data").val();
                fetchData(periode);
            });

            // Event klik tombol export CSV
            $("#exportCsv").click(function() {
                let periode = $("#periode_data").val();
                fetchData(periode, exportToCSV); // Panggil fetchData lalu export CSV setelah data diambil
            });
        });
    </script>
</body>

<?php $this->endSection() ?>
