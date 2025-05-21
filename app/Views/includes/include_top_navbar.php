<?php $uri = service('uri'); ?>

<div class="header-top-wrapper">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="<?= esc(base_url('/home')); ?>">Navbar</a>                
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFlavia" aria-controls="navbarFlavia" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse navbarNav-cstm" id="navbarFlavia">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= ($uri->getSegment(1) == 'home' || $uri->getSegment(1) == '') ? 'active' : ''; ?>" href="<?= esc('/home'); ?>">
                            HOME
                            <span class="line-menu"></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($uri->getSegment(1) == 'qris' || $uri->getSegment(1) == 'scan') ? 'active' : ''; ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            SCAN
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item <?= ($uri->getQuery() == 'card_type=byu') ? 'active' : ''; ?>" href="<?= esc(base_url('/qris?card_type=byu')); ?>">BYU</a>
                            <a class="dropdown-item <?= ($uri->getQuery() == 'card_type=perdana') ? 'active' : ''; ?>" href="<?= esc(base_url('/qris?card_type=perdana')); ?>">PERDANA</a>
                        </div>
                    </li>
                   
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($uri->getSegment(1) == 'report' || $uri->getSegment(1) == 'report_np') ? 'active' : ''; ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            REPORT
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item <?= ($uri->getSegment(1) == 'report') ? 'active' : ''; ?>" href="<?= esc(base_url('/report')); ?>">SCAN RESULT</a>
                            <?php if(session()->get('user_level') == 'admin'){ ?>
                                <a class="dropdown-item <?= ($uri->getSegment(1) == 'report') ? 'active' : ''; ?>" href="<?= esc(base_url('/report')); ?>">FLAVIA BALI TENGAH</a>
                                <a class="dropdown-item <a class="nav-link <?= ($uri->getSegment(1) == 'report_np') ? 'active' : ''; ?>" href="<?= esc(base_url('/report_np/admin_report')); ?>">FLAVIA NEW PROGRAM</a>
                            <?php } ?>
                        </div>
                    </li>
                </ul>
                <a href="<?= esc(base_url('/login/logout')); ?>" class="ms-auto logout-fa">
                    <span class="pe-2">LOGOUT</span><i class="fa-solid fa-right-from-bracket"></i>
                </a>
                
            </div>
        </nav>
        <a class="navbar-brand position-absolute top-0 ps-md-3 pe-md-3 ps-2 pe-2 bg-white navbar-cstm" href="<?= esc(base_url('/home'));?>">
            <img src="<?= esc('/img/flavia_logo_banner_nav.png'); ?>" class="d-inline-block align-top img-fluid" alt="flavia nav logo">
        </a>
    </div>
</div>