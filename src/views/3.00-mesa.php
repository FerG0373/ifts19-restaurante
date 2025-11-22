<?php
// Variables disponibles desde el Controller:
// $mesas (array de MesaVistaDTOs), $titulo, $ubicacionActiva, $exito, etc.

// Función para obtener la clase CSS del estado operativo
function getEstadoClase(string $estadoMesa): string {
    return match ($estadoMesa) {
        'libre' => 'estado-libre',
        'ocupada' => 'estado-ocupada',
        'reservada' => 'estado-reservada',
        'inhabilitada' => 'estado-inhabilitada',
        default => '',
    };
}
// Función para obtener el nombre del tab
function getUbicacionNombre(string $ubicacion): string {
    // Si agregas más ubicaciones en el futuro, solo modificas este match
    return match ($ubicacion) {
        'salon' => 'SALÓN PRINCIPAL',
        'exterior' => 'EXTERIOR',
        default => strtoupper($ubicacion),
    };
}
?>

<!-- Contenedor principal de la vista -->
<div class="contenedor-principal">
    
    <!-- Título y Mensajes -->
    <h1><?php echo htmlspecialchars($titulo); ?></h1>

    <?php if (!empty($exito)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($exito); ?>
        </div>
    <?php endif; ?>

    <!-- Botón de Alta -->
    <div class="header-acciones">
        <a href="mesa/formulario/alta" class="btn-nueva-mesa">
            <i class="fas fa-plus"></i> Nueva Mesa
        </a>
    </div>

    <!-- Pestañas (Tabs) de Ubicación -->
    <div class="tabs">
        <!-- Tab: Salón Principal -->
        <a href="mesa?ubicacion=salon" class="tab-item <?php echo ($ubicacionActiva === 'salon') ? 'activo' : ''; ?>">
            <?php echo getUbicacionNombre('salon'); ?>
        </a>
        
        <!-- Tab: Exterior -->
        <a href="mesa?ubicacion=exterior" class="tab-item <?php echo ($ubicacionActiva === 'exterior') ? 'activo' : ''; ?>">
            <?php echo getUbicacionNombre('exterior'); ?>
        </a>
    </div>

    <!-- Contenedor del Tablero de Mesas -->
    <?php if (empty($mesas)): ?>
        <div class="alert alert-info">No se encontraron mesas en <?php echo getUbicacionNombre($ubicacionActiva); ?>.</div>
    <?php else: ?>
        <div class="tablero-mesas">
            <!-- Bucle sobre los DTOs de Mesa -->
            <?php foreach ($mesas as $mesa): ?>
                <div class="card-mesa" data-id="<?php echo $mesa->id; ?>">
                    <!-- Número de Mesa -->
                    <h2>Mesa <?php echo htmlspecialchars($mesa->nroMesa); ?></h2>
                    
                    <!-- Capacidad -->
                    <div class="card-info-capacidad">
                        <i class="fa-solid fa-user-group icon-fa-persona"></i>
                        <span><?php echo htmlspecialchars($mesa->capacidad); ?> personas</span>
                    </div>
                    
                    <!-- Estado Operativo -->
                    <?php 
                        // Usamos ->value para obtener el string del Enum (ej: 'libre')
                        $claseEstado = getEstadoClase($mesa->estadoMesa->value);
                        $textoEstado = strtoupper($mesa->estadoMesa->value);
                    ?>
                    <div class="estado-mesa <?php echo $claseEstado; ?>">
                        <?php echo htmlspecialchars($textoEstado); ?>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>