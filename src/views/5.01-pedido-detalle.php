<?php
use App\DTOs\PedidoVistaDTO;
use App\DTOs\DetallePedidoDTO;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo); ?></h2>
        <a href="<?= APP_BASE_URL ?>pedido" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <?php
    /** @var PedidoVistaDTO $pedido */
    if (empty($pedido)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje ?? 'No se encontró el pedido.'); ?></div>
    <?php else:
        // Definir clase de badge según el estado
        $badgeClass = match($pedido->estadoPedido) {
            'PENDIENTE' => 'bg-warning text-dark',
            'PREPARACION' => 'bg-info',
            'LISTO' => 'bg-success',
            'ENTREGADO' => 'bg-secondary',
            'CANCELADO' => 'bg-danger',
            default => 'bg-secondary'
        };
    ?>
        <!-- INFORMACIÓN DEL PEDIDO -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Información del Pedido</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Número de Pedido:</dt>
                            <dd class="col-sm-7 fw-bold">#<?php echo htmlspecialchars($pedido->id); ?></dd>
                            
                            <dt class="col-sm-5">Mesa:</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($pedido->numeroMesa); ?></span>
                            </dd>
                            
                            <dt class="col-sm-5">Mozo:</dt>
                            <dd class="col-sm-7"><?php echo htmlspecialchars($pedido->nombreMozo); ?></dd>
                            
                            <dt class="col-sm-5">Fecha y Hora:</dt>
                            <dd class="col-sm-7"><?php echo htmlspecialchars($pedido->fechaHora); ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Tipo de Pedido:</dt>
                            <dd class="col-sm-7"><?php echo htmlspecialchars($pedido->tipoPedido); ?></dd>
                            
                            <dt class="col-sm-5">Estado:</dt>
                            <dd class="col-sm-7">
                                <span class="badge <?php echo $badgeClass; ?> fs-6">
                                    <?php echo htmlspecialchars($pedido->estadoPedido); ?>
                                </span>
                            </dd>
                            
                            <dt class="col-sm-5">Total:</dt>
                            <dd class="col-sm-7 text-success fw-bold fs-4">$<?php echo htmlspecialchars($pedido->total); ?></dd>
                            
                            <?php if ($pedido->observaciones): ?>
                                <dt class="col-sm-5">Observaciones:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($pedido->observaciones); ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTÓN GENERAR/VER FACTURA -->
<?php if (in_array($pedido->estadoPedido, ['LISTO', 'ENTREGADO'])): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-body bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">
                        <i class="fas fa-file-invoice-dollar text-primary"></i> Facturación
                    </h5>
                    <p class="mb-0 text-muted">Este pedido está listo para ser facturado</p>
                </div>
                <div>
                    <form method="POST" action="<?= APP_BASE_URL ?>factura/generar" class="d-inline me-2">
                        <input type="hidden" name="pedido_id" value="<?php echo $pedido->id; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-receipt"></i> Generar Factura
                        </button>
                    </form>
                    <a href="<?= APP_BASE_URL ?>factura/ver?pedido_id=<?php echo $pedido->id; ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Ver Factura
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
        <!-- DETALLE DE PRODUCTOS -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Productos del Pedido</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Precio Unit.</th>
                                <th class="text-center">Subtotal</th>
                                <th>Instrucciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /** @var DetallePedidoDTO $detalle */
                            foreach ($pedido->detalles as $detalle):
                            ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($detalle->nombreProducto); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-dark"><?php echo $detalle->cantidad; ?></span>
                                </td>
                                <td class="text-center text-muted">
                                    $<?php echo number_format($detalle->precioUnitario, 2, ',', '.'); ?>
                                </td>
                                <td class="text-center text-success fw-bold">
                                    $<?php echo htmlspecialchars($detalle->subtotal); ?>
                                </td>
                                <td class="text-muted fst-italic">
                                    <?php echo htmlspecialchars($detalle->instruccionesPreparacion ?: '-'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                <td class="text-center text-success fw-bold fs-5">
                                    $<?php echo htmlspecialchars($pedido->total); ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ACCIONES DE CAMBIO DE ESTADO -->
        <?php if (!in_array($pedido->estadoPedido, ['ENTREGADO', 'CANCELADO'])): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title">Cambiar Estado del Pedido</h5>
                    <div class="btn-group" role="group">
                        <?php if ($pedido->estadoPedido === 'PENDIENTE'): ?>
                            <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $pedido->id; ?>">
                                <input type="hidden" name="nuevo_estado" value="preparacion">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-fire"></i> Pasar a Preparación
                                </button>
                            </form>
                            <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado" class="d-inline ms-2">
                                <input type="hidden" name="id" value="<?php echo $pedido->id; ?>">
                                <input type="hidden" name="nuevo_estado" value="cancelado">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancelar Pedido
                                </button>
                            </form>
                        <?php elseif ($pedido->estadoPedido === 'PREPARACION'): ?>
                            <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $pedido->id; ?>">
                                <input type="hidden" name="nuevo_estado" value="listo">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Marcar como Listo
                                </button>
                            </form>
                            <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado" class="d-inline ms-2">
                                <input type="hidden" name="id" value="<?php echo $pedido->id; ?>">
                                <input type="hidden" name="nuevo_estado" value="cancelado">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancelar Pedido
                                </button>
                            </form>
                        <?php elseif ($pedido->estadoPedido === 'LISTO'): ?>
                            <form method="POST" action="<?= APP_BASE_URL ?>pedido/cambiar-estado" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $pedido->id; ?>">
                                <input type="hidden" name="nuevo_estado" value="entregado">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-check-double"></i> Marcar como Entregado
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>