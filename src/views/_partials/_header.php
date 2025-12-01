<?php 
// Las variables $estaAutenticado, $perfilAcceso y $usuarioDni están disponibles aquí.
// Helper para determinar si mostrar un enlace (Mantener la función debeMostrarEnlace aquí si la definiste antes).
function debeMostrarEnlace(string $url, bool $estaAutenticado, ?string $perfilAcceso): bool {
    // Rutas públicas 
    $rutasPublicas = ['login']; 
    // Rutas accesibles por Mozo (y Encargado)
    $rutasMozo = ['home', 'mesas', 'logout']; // 'logout' debe ser accesible para todos los logueados.
    // Rutas accesibles solo por Encargado
    $rutasEncargado = ['personal'];

    if (!$estaAutenticado) {
        return in_array($url, $rutasPublicas);
    }    
    // Si está autenticado:
    if (in_array($url, $rutasMozo)) {
        return true; 
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
                // Navegación principal (filtrada por rol)
                $rutasNav = $viewRenderer->rutasNav; 
                
                foreach ($rutasNav as $url => $rutaArchivo) {
                    // Excluimos 'logout' de este bucle para ponerlo en el desplegable.
                    if ($url !== 'logout' && debeMostrarEnlace($url, $estaAutenticado, $perfilAcceso)) {
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
                <?php if ($estaAutenticado): ?>
                    <li class="nav-item dropdown">
                        
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> 
                            <?php 
                            // Muestra el DNI o el Perfil de Acceso como fallback
                            echo htmlspecialchars(strtoupper($usuarioDni ?? $perfilAcceso ?? 'USUARIO')); 
                            ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="<?php echo APP_BASE_URL; ?>personal/mi-detalle">
                                    Ver Detalle
                                </a>
                            </li>
                            
                            <li>
                                <a class="dropdown-item" href="<?php echo APP_BASE_URL; ?>logout">
                                    Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_BASE_URL; ?>login">
                            <i class="fas fa-sign-in-alt"></i> 
                            Iniciar Sesión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>