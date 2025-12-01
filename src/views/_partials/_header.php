<?php 
// IMPORTANTE: $estaAutenticado y $perfilAcceso ya están disponibles aquí gracias a extract($datos) en ViewRenderer.

// Helper para determinar si mostrar un enlace. Útil para separar lógica en el NAV.
function debeMostrarEnlace(string $url, bool $estaAutenticado, ?string $perfilAcceso): bool {
    // Rutas públicas (se ven si NO estás autenticado)
    $rutasPublicas = ['login'];
    // Rutas accesibles por Mozo (y Encargado por herencia)
    $rutasMozo = ['home', 'mesas', 'logout'];
    // Rutas accesibles solo por Encargado
    $rutasEncargado = ['personal'];

    if (!$estaAutenticado) {
        return in_array($url, $rutasPublicas);
    }
    
    // Si está autenticado:
    if (in_array($url, $rutasMozo)) {
        return true; // Todos los logueados ven Mozo/Home
    }
    if (in_array($url, $rutasEncargado)) {
        return $perfilAcceso === 'encargado';
    }
    
    return false;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?=APP_BASE_URL?>">App Restaurante</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php
                // Recorremos las rutas marcadas con 'nav' => true
                $rutasNav = $viewRenderer->rutasNav; // Usamos $viewRenderer para acceder a la propiedad
                
                foreach ($rutasNav as $url => $rutaArchivo) {
                    if (debeMostrarEnlace($url, $estaAutenticado, $perfilAcceso)) {
                        $nombreEnlace = ucfirst($url);
                ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_BASE_URL, $url; ?>">
                                <?php echo htmlspecialchars($nombreEnlace); ?>
                            </a>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <?php if ($estaAutenticado): ?>
                        <a class="nav-link" href="#">
                            <i class="fas fa-user"></i> 
                            <?php echo htmlspecialchars(strtoupper($usuarioDni ?? $perfilAcceso ?? 'USUARIO')); ?>
                        </a>
                    <?php else: ?>
                        <a class="nav-link" href="<?php echo APP_BASE_URL; ?>login">
                            <i class="fas fa-sign-in-alt"></i> 
                            INICIAR SESIÓN
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>