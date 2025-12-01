<?php
use App\DTOs\PersonalVistaDTO;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h2 class="text-primary mb-0 me-3"><?php echo htmlspecialchars($titulo); ?></h2>
            
            <?php
            // Mostrar el botón Editar SOLO si NO es "Mi Detalle" (es decir, el Administrador viendo a alguien más).
            if (!isset($esMiDetalle) || $esMiDetalle === false): ?>
                <form action="<?= APP_BASE_URL ?>personal/formulario/cargar" method="POST" class="d-inline">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($personal->id); ?>">
                    <button type="submit" class="btn btn-link fa-lg mt-2" title="Editar Información">
                        <i class="fas fa-edit"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php
        // El botón es visible siempre, pero su acción cambia según la ruta:
        if (isset($esMiDetalle) && $esMiDetalle === true): ?>
            <!-- MODO "MI DETALLE": El botón usa JavaScript para ir a la página anterior del historial. -->
            <button onclick="history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        <?php else: ?>
            <!-- MODO "DETALLE DE ADMIN": El botón enlaza directamente al listado de personal. -->
            <a href="<?=APP_BASE_URL?>personal" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        <?php endif; ?>
    </div>

    <?php
    if (empty($personal)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje ?? 'No se encontró el personal.'); ?></div>
    <?php else: ?>
        <div class="card shadow-sm mx-auto mt-5" style="max-width: 600px;">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Información Personal y Laboral</h4>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    
                    <dt class="col-sm-4 text-nowrap">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->id); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">DNI:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->dni); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Nombre:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->nombre); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Apellido:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->apellido); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Fecha de Nacimiento:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->fechaNacimiento); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Email:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->email); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Teléfono:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->telefono); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Sexo:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->sexo); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Puesto:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->puesto); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Fecha de Contratación:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->fechaContratacion); ?></dd> 
                    
                    <dt class="col-sm-4 text-nowrap">Activo:</dt>
                    <dd class="col-sm-8">
                        <?php if ($personal->activo): ?>
                            <span class="text-success fw-bold">Sí</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">No</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    <?php endif; ?>
</div>

