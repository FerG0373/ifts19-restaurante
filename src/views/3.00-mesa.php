<?php
// Variables disponibles desde el Controller:
// $mesas (array de MesaVistaDTOs), $titulo, $ubicacionActiva, $exito, $mozos (array de Objetos Personal), etc.

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
    return match ($ubicacion) {
        'salon' => 'SALÓN PRINCIPAL',
        'exterior' => 'EXTERIOR',
        default => strtoupper($ubicacion),
    };
}
?>

<div class="contenedor-principal">
    
    <h2 class="text-primary mt-5 mb-4 me-3"><?php echo htmlspecialchars($titulo); ?></h2>

    <?php if (!empty($exito)): ?>
        <div class="alert alert-success autoclose-alert" role="alert">
            <?php echo htmlspecialchars($exito); ?>
        </div>
    <?php endif; ?>

    <div class="header-acciones">
        <a href="mesas/formulario" class="btn-nueva-mesa">
            <i class="fas fa-plus"></i> Nueva Mesa
        </a>
    </div>

    <div class="tabs">
        <a href="mesas" class="tab-item <?php echo ($ubicacionActiva === 'salon') ? 'activo' : ''; ?>">
            <?php echo getUbicacionNombre('salon'); ?>
        </a>
        
        <a href="mesas?ubicacion=exterior" class="tab-item <?php echo ($ubicacionActiva === 'exterior') ? 'activo' : ''; ?>">
            <?php echo getUbicacionNombre('exterior'); ?>
        </a>
    </div>

    <?php if (empty($mesas)): ?>
        <div class="alert alert-info">No se encontraron mesas en <?php echo getUbicacionNombre($ubicacionActiva); ?>.</div>
    <?php else: ?>
        <div class="tablero-mesas">
            <?php foreach ($mesas as $mesa): ?>
                <?php if ($mesa->activo): // Solo si la mesa está activa ?>
                <div class="card-mesa position-relative" data-id="<?php echo $mesa->id; ?>">
                    
                    <?php if ($mesa->estadoMesa === 'libre' && isset($mozos) && !empty($mozos)): ?>
                        <?php // DESPLEGABLE ?>
                        <form 
                            method="POST" 
                            action="<?php echo APP_BASE_URL; ?>mesas/asignarMozo" 
                            class="form-asignar-mozo"
                            style="position: absolute; top: 8px; right: 8px; z-index: 10;">
                            <input type="hidden" name="id_mesa" value="<?php echo $mesa->id; ?>">
                            <select name="id_personal" class="form-select form-select-sm" required style="width: auto; display: inline-block;">
                                <option value="" selected disabled>Asignar Mozo...</option>
                                
                                <?php foreach ($mozos as $mozo): ?>
                                    <option value="<?php echo $mozo->getId(); ?>">
                                        <?php echo htmlspecialchars($mozo->getNombre() . ' ' . $mozo->getApellido()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary ms-2" title="Confirmar Asignación">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <?php // ELIMINAR ?>
                        <form 
                            method="POST" 
                            action="<?php echo APP_BASE_URL; ?>mesas/eliminar" 
                            onsubmit="return confirm('¿Estás seguro de que deseas dar de baja la Mesa N° <?php echo htmlspecialchars($mesa->nroMesa); ?>? Esta acción la dejará inactiva.');" 
                            class="position-absolute"
                            style="right: 8px; bottom: 8px;">
                            <input type="hidden" name="id" value="<?php echo $mesa->id; ?>">
                            <button 
                                type="submit"
                                class="btn-eliminar-mesa text-danger"
                                title="Dar de baja la mesa (Eliminación Lógica)"
                                style="background: none; border: none; padding: 0;">
                                <i class="fas fa-trash-alt fa-sm"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <h2 class="mt-4">Mesa <?php echo htmlspecialchars($mesa->nroMesa); ?></h2>
                    
                    <div class="card-info-capacidad">
                        <i class="fa-solid fa-user-group icon-fa-persona"></i>
                        <span><?php echo htmlspecialchars($mesa->capacidad); ?> personas</span>
                    </div>
                    
                    <?php 
                        $claseEstado = getEstadoClase($mesa->estadoMesa);
                        $textoEstado = strtoupper($mesa->estadoMesa);
                    ?>
                    <div class="estado-mesa <?php echo $claseEstado; ?>">
                        <?php echo htmlspecialchars($textoEstado); ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>