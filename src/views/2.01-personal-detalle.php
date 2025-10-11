<?php
use App\Models\Personal;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo); ?></h2>
        
        <a href="<?=APP_BASE_URL?>personal" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <?php /** @var Personal $personal */
    if (empty($personal)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php else: ?>
        <div class="card shadow-sm mx-auto mt-5" style="max-width: 600px;">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Información personal</h4>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-nowrap">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getId()); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">DNI:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getDni()); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Nombre:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getNombre()); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Apellido:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getApellido()); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Fecha de Nacimiento:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getFechaNacimiento()->format('d/m/Y')); ?></dd>

                    <dt class="col-sm-4 text-nowrap">Email:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getEmail()); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Teléfono:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getTelefono()); ?></dd>                    
                    
                    <dt class="col-sm-4 text-nowrap">Sexo:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getSexo()->name); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Puesto:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getPuesto()->name); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Fecha de Contratación:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($personal->getFechaContratacion()->format('d/m/Y')); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Activo:</dt>
                    <dd class="col-sm-8">
                        <?php if ($personal->isActivo()): ?>
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