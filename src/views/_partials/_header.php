<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?=APP_BASE_URL?>">App Restaurante</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>   

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php
                $rutasNav = $this->rutasNav;
                foreach ($rutasNav as $url => $rutaArchivo) {
                    $nombreEnlace = ucfirst($url);
                ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_BASE_URL, $url; ?>">
                            <?php echo htmlspecialchars($nombreEnlace); ?>
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-user"></i> USUARIO</a>
                </li>
            </ul>            
        </div>
        
    </div>
</nav>

