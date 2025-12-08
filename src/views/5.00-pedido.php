<?php
use App\DTOs\PedidoVistaDTO;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h2 class="text-primary mb-0 me-3">
                <?php echo htmlspecialchars($titulo ?? 'Pedidos'); ?>
            </h2>
        </div>
        <a href="<?= APP_BASE_URL ?>pedido/formulario" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nuevo Pedido
        </a>
    </div>

    <!-- FILTROS POR ESTADO -->
    <div class="btn-group mb-3" role="group">
        <a href="<?= APP_BASE_URL ?>pedido" 
           class="btn btn-sm <?= $filtroEstado === 'todos' ? 'btn-primary' : 'btn-outline-primary' ?>">
            Todos
        </a>
        <a href="<?= APP_BASE_URL ?>pedido?estado=pendiente" 
           class="btn btn-sm <?= $filtroEstado === 'pendiente' ? 'btn-warning' : 'btn-outline-warning' ?>">
            Pendientes
        </a>
        <a href="<?= APP_BASE_URL ?>pedido?estado=preparacion" 
           class="btn btn-sm <?= $filtroEstado === 'preparacion' ? 'btn-info' : 'btn-outline-info' ?>">
            En Preparación
        </a>
        <a href="<?= APP_BASE_URL ?>pedido?estado=listo" 
           class="btn btn-sm <?= $filtroEstado === 'listo' ? 'btn-success' : 'btn-outline-success' ?>">
            Listos
        </a>
        <a href="<?= APP_BASE_URL ?>pedido?estado=entregado" 
           class="btn btn-sm <?= $filtroEstado === 'entregado' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
            Entregados
        </a>
        <a href="<?= APP_BASE_URL ?>pedido?estado=cancelado" 
           class="btn btn-sm <?= $filtroEstado === 'cancelado' ? 'btn-danger' : 'btn-outline-danger' ?>">
            Cancelados
        </a>
    </div>

    <?php if (!empty($exito)): ?>
        <div class="alert alert-success alert-dismissible fade show autoclose-alert" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($exito); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">No hay pedidos registrados con estos filtros.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Mesa</th>
                        <th class="text-center">Mozo</th>
                        <th class="text-center">Fecha/Hora</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    /** @var PedidoVistaDTO $p */
                    foreach ($pedidos as $p):
                        // Definir clase de badge según el estado
                        $badgeClass = match($p->estadoPedido) {
                            'PENDIENTE' => 'bg-warning text-dark',
                            'PREPARACION' => 'bg-info',
                            'LISTO' => 'bg-success',
                            'ENTREGADO' => 'bg-secondary',
                            'CANCELADO' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    ?>
                    <tr>
                        <td class="text-center fw-bold"><?php echo $p->id; ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($p->numeroMesa); ?></span>
                        </td>
                        <td class="text-center"><?php echo htmlspecialchars($p->nombreMozo); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($p->fechaHora); ?></td>
                        <td class="text-center">
                            <small class="text-muted"><?php echo htmlspecialchars($p->tipoPedido); ?></small>
                        </td>
                        <td class="text-center text-success fw-bold">$<?php echo htmlspecialchars($p->total); ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($p->estadoPedido); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Ver detalle -->
                                <form method="POST" action="<?= APP_BASE_URL ?>pedido/detalle" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $p->id; ?>">
                                    <button type="submit" class="btn btn-outline-primary" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </form>
                                
                                <!-- Cambiar estado (solo si no está entregado o cancelado) -->
                                <?php if (!in_array($p->estadoPedido, ['ENTREGADO', 'CANCELADO'])): ?>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if ($p->estadoPedido === 'PENDIENTE'): ?>
                                                <li>
                                                    <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado">
                                                        <input type="hidden" name="id" value="<?php echo $p->id; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="preparacion">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-fire text-info"></i> En Preparación
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado">
                                                        <input type="hidden" name="id" value="<?php echo $p->id; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="cancelado">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-times text-danger"></i> Cancelar
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php elseif ($p->estadoPedido === 'PREPARACION'): ?>
                                                <li>
                                                    <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado">
                                                        <input type="hidden" name="id" value="<?php echo $p->id; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="listo">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check text-success"></i> Listo
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado">
                                                        <input type="hidden" name="id" value="<?php echo $p->id; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="cancelado">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-times text-danger"></i> Cancelar
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php elseif ($p->estadoPedido === 'LISTO'): ?>
                                                <li>
                                                    <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado">
                                                        <input type="hidden" name="id" value="<?php echo $p->id; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="entregado">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check-double text-secondary"></i> Entregado
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>