<?php
use App\DTOs\ProductoVistaDTO;
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
                        <span class="text-secondary" title="Mostrar también productos inactivos">Ver Todos <i class="fas fa-eye"></i></span>
                    <?php else: ?>
                        <span class="text-info" title="Actualmente mostrando productos activos e inactivos">Solo Activos <i class="fas fa-check-circle"></i></span>
                    <?php endif; ?>
                </label>
            </div>
        </div>
        <a href="<?= APP_BASE_URL ?>producto/formulario" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Agregar Producto
        </a>
    </div>

    <?php if (!empty($exito)): ?>
        <div class="alert alert-success alert-dismissible fade show autoclose-alert" role="alert">
            <i class="fas fa-check-circle"></i> Operación Exitosa: <?php echo htmlspecialchars($exito); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">No hay productos registrados en el sistema.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Categoría</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Activo</th>
                        <th class="text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    /** @var ProductoVistaDTO $p */
                    foreach ($productos as $p):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo htmlspecialchars($p->nombre); ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($p->categoria); ?></span>
                        </td>
                        <td class="text-center text-success fw-bold">$<?php echo htmlspecialchars($p->precio); ?></td>
                        <td class="text-center">
                            <?php if ($p->cantidadStock > 10): ?>
                                <span class="badge bg-success"><?php echo $p->cantidadStock; ?></span>
                            <?php elseif ($p->cantidadStock > 0): ?>
                                <span class="badge bg-warning"><?php echo $p->cantidadStock; ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Sin stock</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($p->activo): ?>
                                <span class="badge bg-success">Sí</span>
                            <?php else: ?>
                                <span class="badge bg-danger">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="producto/detalle">
                                <input type="hidden" name="id" value="<?php echo $p->id; ?>">
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