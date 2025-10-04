<?php 
/**
 * Vista de Listado de Personal (2.00-personal.php)
 * Recibe: $titulo (string) y $personal (array de objetos App\Models\Personal)
 */
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo ?? 'Listado'); ?></h2>
        <a href="personal/alta" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Agregar Personal
        </a>
    </div>

    <?php if (empty($personal)): ?>
        <div class="alert alert-info">
            No hay personal registrado en el sistema.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Puesto</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Contratación</th>
                        <th class="text-center">Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    /** @var App\Models\Personal $p */
                    foreach ($personal as $p): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p->getId()); ?></td>
                        <td>
                            <?php echo htmlspecialchars($p->getNombre() . ' ' . $p->getApellido()); ?>
                        </td>
                        <td><?php echo htmlspecialchars($p->getPuesto()->name); ?></td>
                        <td><?php echo htmlspecialchars($p->getDni()); ?></td>
                        <td><?php echo htmlspecialchars($p->getEmail()); ?></td>
                        <td><?php echo htmlspecialchars($p->getFechaContratacion()->format('d/m/Y')); ?></td>
                        
                        <td class="text-center">
                            <?php if ($p->isActivo()): ?>
                                <span class="badge bg-success">Sí</span>
                            <?php else: ?>
                                <span class="badge bg-danger">No</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center">
                            <a href="personal/detalle?id=<?php echo $p->getId(); ?>" class="btn btn-sm btn-info" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="personal/editar?id=<?php echo $p->getId(); ?>" class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="personal/baja" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $p->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-<?php echo $p->isActivo() ? 'danger' : 'success'; ?>" title="<?php echo $p->isActivo() ? 'Dar de Baja' : 'Dar de Alta'; ?>">
                                    <i class="fas fa-<?php echo $p->isActivo() ? 'user-slash' : 'user-plus'; ?>"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>