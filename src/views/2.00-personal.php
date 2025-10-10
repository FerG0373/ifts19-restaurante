<?php
use App\Models\Personal;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">        
        <div class="d-flex align-items-center">
            <h2 class="text-primary mb-0 me-3">
                <?php echo htmlspecialchars($titulo ?? 'Listado'); ?>
            </h2>

            <div class="form-check form-switch pt-1 mt-2 ms-2">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    role="switch" 
                    id="toggleActivos"
                    <?= $esActivo ? '' : 'checked' ?> 
                    onchange="window.location.href = this.checked ? '<?= $urlVerTodos ?>' : '<?= $urlVerActivos ?>';"
                >
                <label class="form-check-label small" for="toggleActivos">
                    <?php if ($esActivo): ?>
                        <span class="text-secondary" title="Mostrar también el personal inactivo">Ver Todos <i class="fas fa-eye"></i></span>
                    <?php else: ?>
                        <span class="text-info" title="Actualmente mostrando personal activo e inactivo">Solo Activos <i class="fas fa-user-check"></i></span>
                    <?php endif; ?>
                </label>
            </div>
        </div>
        <a href="personal/alta" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Agregar Personal
        </a>
    </div>

    <?php if (empty($personal)): ?>
        <div class="alert alert-info">No hay personal registrado en el sistema.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nombre Completo</th>
                        <th class="text-center">Puesto</th>
                        <th class="text-center">DNI</th>
                        <th class="text-center">Activo</th>
                        <th class="text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    /** @var Personal $p */
                    foreach ($personal as $p): 
                    ?>
                    <tr>
                        <td class="text-center"><?php echo htmlspecialchars($p->getId()); ?></td>
                        <td class="text-center">
                            <?php echo htmlspecialchars($p->getApellido() . ', ' . $p->getNombre()); ?>
                        </td>
                        <td class="text-center"><?php echo htmlspecialchars($p->getPuesto()->name); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($p->getDni()); ?></td>
                        <td class="text-center">
                            <?php if ($p->isActivo()): ?>
                                <span class="badge bg-success">Sí</span>
                            <?php else: ?>
                                <span class="badge bg-danger">No</span>
                            <?php endif; ?>
                        </td>                        
                        <td class="text-center">
                            <form method="POST" action="personal/detalle">
                                <input type="hidden" name="id" value="<?php echo $p->getId(); ?>">
                                <button type="submit" class="btn btn-link text-decoration-none p-0 border-0 bg-transparent" title="Ver detalle">🔍</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>