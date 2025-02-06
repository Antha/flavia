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
                        <h5 class="text-abs-left">REWARD</h5>
                        <div class="col-12">
                            <form method="post" action="<?= esc(base_url('/report/admin_report'));?>" enctype="multipart/form-data">
                                <?php csrf_field() ?>
                                <div class="row no-gutters">
                                    <div class="form-group col-md-3 col-4 pe-2" id="col_periode_data">
                                        <div class="input-group dropdown_input">
                                            <input required type="text" class="monthPicker form-control pull-left txt-input-data" id="periode_data" name="periode_data_reward" value="<?= esc($periode_data_reward_display); ?>" style="font-size: 14px;"/>
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-3 ps-0" id="wrap_kip_filter_branch">
                                        <select name='filter_branch_reward' id='filter_branch_reward' class="select_filter pb-2 pt-2" title="Area Type" style="width:100%;">
                                            <option value="" selected disabled>Branch</option>
                                            <option value="ALL">ALL BRANCH</option>
                                            <option value="DENPASAR">DENPASAR</option>
                                            <option value="FLORES">FLORES</option>
                                            <option value="KUPANG">KUPANG</option>
                                            <option value="MATARAM">MATARAM</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 col-3 ps-0" id="wrap_kip_filter_cluster">
                                        <select name='filter_cluster_reward' id='filter_cluster_reward' class="select_filter pb-2 pt-2" title="Area Type" style="width:100%;">
                                            <option value="" selected disabled>Cluster</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-2 ps-0">			
                                        <input type="submit" id="btn_submit_filter_reward" name="btn_submit_filter_reward" value="GO" class="submit_btn_datepicker rounded float-start">
                                    </div>

                                    <p class="flashdata_error"><?= session()->getFlashdata('table_not_exists'); ?></p> 
                                    
                                    <div style="clear: both;"></div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12">
                            <div class="table-report-wrapper">
                                <div class="text-center">
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                            <tr class="header-top-wrapper">
                                                <th>No</th>
                                                <th>REWARD</th>
                                                <th>BRANCH</th>
                                                <th>CLUSTER</th>
                                                <th>PERIODE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($dataIsExists == 'true'){ ?>
                                                <?php $i=1;foreach($allRewardItem as $rows){ ?>
                                                    <tr>
                                                        <td><?= esc($i); ?></td>
                                                        <td><?= esc($rows['item']); ?></td>
                                                        <td><?= esc($rows['branch']); ?></td>
                                                        <td><?= esc($rows['cluster']); ?></td>
                                                        <td><?= esc($rows['periode']); ?></td>
                                                    </tr>
                                                <?php $i++;} ?>
                                            <?php }else{ ?>
                                                <tr>
                                                    <td colspan="6">No Data Available</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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

        $('#filter_branch_reward').on('change', function() {
            let branch = $(this).val();
            let clusterDropdown = $('#filter_cluster_reward');
            
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
</script>

<?php $this->endSection() ?>
