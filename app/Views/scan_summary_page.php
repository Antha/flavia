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

            <div class="reward-info-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <div class="reward-box position-relative h-100">
                                <h5 class="text-abs-left position-absolute p-2">REWARD</h5>
                                <div class="reward-list pt-3 ps-3 pe-3 justify-content-center">
                                    <div class="row reward-item pb-1 pt-1">
                                        <div class="col-10">
                                            <span class="reward-title">
                                                1. Reward number one
                                            </span></div>
                                        <div class="col-2 text-end">10000 pts</div>
                                    </div>
                                    <div class="row reward-item pb-1 pt-1">
                                        <div class="col-10">2. Reward number two</div>
                                        <div class="col-2 text-end">10000 pts</div>
                                    </div>
                                    <div class="row reward-item pb-1 pt-1">
                                        <div class="col-10">3. Reward number three</div>
                                        <div class="col-2 text-end">10000 pts</div>
                                    </div>
                                    <div class="row reward-item pb-1 pt-1">
                                        <div class="col-10">4. Reward number four</div>
                                        <div class="col-2 text-end">10000 pts</div>
                                    </div>
                                    <div class="row reward-item pb-1 pt-1">
                                        <div class="col-10">5. Reward number five</div>
                                        <div class="col-2 text-end">10000 pts</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="row justify-content-center">
                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1 mx-auto"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">SCAN</span>
                                        <span class="w-100 d-block menu-item-text3">BYU</span>
                                        <span class="w-100 d-block mt-3 mb-3 menu-item-text1">500</span>
                                    </div>
                                </div>

                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">SCAN</span>
                                        <span class="w-100 d-block menu-item-text3">PERDANA</span>
                                        <span class="w-100 d-block mt-3 mb-3 menu-item-text1">500</span>
                                    </div>
                                </div>

                                <div class="col-4 text-center menu-item ps-1 pe-1">
                                    <div class="menu-item-wrapper h-100">
                                        <span class="menu-item-fa1"><i class="fa-solid fa-qrcode"></i></span>
                                        <span class="w-100 d-block menu-item-text3 mt-4">TOTAL</span>
                                        <span class="w-100 d-block menu-item-text3">SCAN</span>
                                        <span class="w-100 d-block mt-3 mb-3 menu-item-text1">1000</span>
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
                        <div class="col-8">
                            <form method="post" action="" enctype="multipart/form-data">
                                <?php csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-4 pe-2" id="col_periode_data">
                                        <div class="input-group dropdown_input">
                                            <input required type="text" class="monthPicker form-control pull-left txt-input-data" id="periode_data" name="periode_data_kpi_admin" value="2025" />
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-3 ps-0" id="wrap_kip_filter_branch">
                                        <select name='kpi_filter_branch_admin' id='kpi_filter_branch_admin' class="select_filter pb-2 pt-2" title="Area Type" style="width:100%;">
                                            <option value="" selected disabled>Branch</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 col-3 ps-0" id="wrap_kip_filter_cluster">
                                        <select name='kpi_filter_cluster_admin' id='kpi_filter_cluster_admin' class="select_filter pb-2 pt-2" title="Area Type" style="width:100%;">
                                            <option value="" selected disabled>Cluster</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-2 ps-0">			
                                        <input type="submit" id="btn_submit_periode_kip" name="btn_submit_periode_kip_admin" value="GO" class="submit_btn_datepicker rounded float-start">
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
                                        <input required type="text" id="searchInput" class="form-control txt-input-data" placeholder="Search..."  onkeyup="filterTable()">
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
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr class="header-top-wrapper">
                                        <th>No</th>
                                        <th>SCAN DATE</th>
                                        <th>MSISDN</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1.</td>
                                        <td>2025-01-06 09:00:22:16</td>
                                        <td>081233944850908</td>
                                        <td>VALID</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>2025-01-06 09:00:22:16</td>
                                        <td>081233944850908</td>
                                        <td>NOT VALID</td>
                                    </tr>
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
    });
</script>

<?php $this->endSection() ?>
