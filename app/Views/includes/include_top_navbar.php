<div class="header-top-wrapper">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="#">Navbar</a>                
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFlavia" aria-controls="navbarFlavia" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse navbarNav-cstm" id="navbarFlavia">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            HOME
                            <span class="line-menu"></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            SCAN
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">BYU</a>
                            <a class="dropdown-item" href="#">PERDANA</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= esc(base_url('/report')); ?>">POINT</a>
                    </li>
                </ul>
                <a href="<?= esc(base_url('/login/logout')); ?>" class="ms-auto logout-fa">
                    <span class="pe-2">LOGOUT</span><i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </nav>
        <a class="navbar-brand position-absolute top-0 ps-md-3 pe-md-3 ps-2 pe-2 bg-white navbar-cstm" href="#">
            <img src="<?= esc('/img/flavia_logo_banner_nav.png'); ?>" class="d-inline-block align-top img-fluid" alt="flavia nav logo">
        </a>
    </div>
</div>