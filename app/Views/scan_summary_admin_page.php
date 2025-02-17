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

            <div class="filter-wrapper mt-md-5 mt-4">
                <div class="container">
                    <div class="row">
                        <div class="col-8">
                            <form method="post" action="<?php echo esc(base_url('/report/admin_report')); ?>" enctype="multipart/form-data">
                                <?php csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-4 pe-2" id="col_periode_data">
                                        <div class="input-group dropdown_input">
                                            <input required type="text" class="monthPicker form-control pull-left txt-input-data" id="periode_data" name="periode_data" value="202502" />
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-3 ps-0" id="wrap_kip_filter_branch">
                                        <select name='filter_branch' id='filter_branch' class="select_filter pb-2 pt-2" title="Area Type" style="width:100%;">
                                            <option value="" selected disabled>Branch</option>
                                            <option value="ALL">ALL</option>
                                            <option value="DENPASAR">DENPASAR</option>
                                            <option value="FLORES">FLORES</option>
                                            <option value="KUPANG">KUPANG</option>
                                            <option value="MATARAM">MATARAM</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 col-3 ps-0" id="wrap_kip_filter_cluster">
                                        <select name='filter_cluster' id='filter_cluster' class="select_filter pb-2 pt-2" title="Area Type" style="width:100%;">
                                            <option value="" selected disabled>Cluster</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-2 ps-0">			
                                        <input type="submit" id="btn_submit" name="btn_submit" value="GO" class="submit_btn_datepicker rounded float-start">
                                    </div>

                                    <p class="flashdata_error"><?= session()->getFlashdata('table_not_exists'); ?></p> 
                                    
                                    <div style="clear: both;"></div>
                                </div>
                            </form>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-9">
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
                                </div>
                                <div class="col-3 ps-0">
                                    <button id="exportCsv" class="submit_btn rounded w-100">DOWNLOAD</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-info">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <span class="me-3">Filter Branch : <?= esc($filterBranch); ?></span>
                            <span>Filter Cluster : <?= esc($filterCluster); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-report-wrapper mt-3">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <table class="table table-responsive table-bordered" id="dataTable">
                                <thead>
                                    <tr class="header-top-wrapper">
                                        <th>No</th>
                                        <th>FL NAME</th>
                                        <th>SCAN DATE</th>
                                        <th>MSISDN</th>
                                        <th>BRANCH</th>
                                        <th>CLUSTER</th>
                                        <th>CARD</th>
                                        <th>STATUS</th>
                                        <th>POINT</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable_body_filter">
                                    <?php $i=1;foreach($resumeScan as $rows){ ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= esc($rows['username']); ?></td>
                                        <td><?= esc($rows['scan_date']); ?></td>
                                        <td><?= esc($rows['msisdn']); ?></td>
                                        <td><?= esc($rows['fl_name']); ?></td>
                                        <td><?= esc($rows['outlet_name']); ?></td>
                                        <td><?= esc($rows['digipos_id']); ?></td>
                                        <td><?= esc($rows['card_type']); ?></td>
                                        <td><?= esc($rows['status_data']); ?></td>
                                        <td><?= esc($rows['POINT']); ?></td>
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

        const clusterOptions = {
            "DENPASAR": ["BALI BARAT", "BALI TENGAH", "BALI TIMUR"],
            "FLORES": ["ENDE SIKKA", "FLORES TIMUR", "MANGGARAI"],
            "KUPANG": ["KUPANG ROTE", "MALAKA TIMTIM BELU", "SUMBA"],
            "MATARAM": ["LOMBOK", "SUMBAWA BARAT", "SUMBAWA TIMUR"]
        };

        $('#filter_branch').on('change', function() {
            let branch = $(this).val();
            let clusterDropdown = $('#filter_cluster');
            
            clusterDropdown.html('<option value="" selected disabled>Cluster</option>');
            if(branch == 'ALL'){
                cityDropdown.html('<option value="" selected disabled>Cluster</option>');
            }
        
            if (clusterOptions[branch]) {
                clusterOptions[branch].forEach(cluster => {
                    clusterDropdown.append(`<option value="${cluster}">${cluster}</option>`);
                });
            }
        
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
