<?php
use App\DTOs\FacturaVistaDTO;
use App\DTOs\PedidoVistaDTO;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo); ?></h2>
        <a href="<?= APP_BASE_URL ?>pedido" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Pedidos
        </a>
    </div>

    <?php if (!empty($exito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($exito); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php
    /** @var FacturaVistaDTO $factura */
    /** @var PedidoVistaDTO $pedido */
    
    // Badge de estado de factura
    $badgeEstado = match($factura->estado) {
        'PENDIENTE' => 'bg-warning text-dark',
        'PAGADA' => 'bg-success',
        'CANCELADA' => 'bg-danger',
        default => 'bg-secondary'
    };
    ?>

    <!-- TARJETA DE FACTURA -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0">
                <!-- ENCABEZADO -->
                <div class="card-header bg-primary text-white py-4">
                    <div class="row">
                        <div class="col-6">
                            <h3 class="mb-0">
                                <i class="fas fa-receipt"></i> FACTURA
                            </h3>
                            <p class="mb-0">Nº <?php echo str_pad($factura->id, 8, '0', STR_PAD_LEFT); ?></p>
                        </div>
                        <div class="col-6 text-end">
                            <h4 class="mb-0">Restaurante</h4>
                            <p class="mb-0 small">CUIT: 20-12345678-9</p>
                        </div>
                    </div>
                </div>

                <!-- CUERPO -->
                <div class="card-body p-4">
                    <!-- INFO GENERAL -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">INFORMACIÓN DEL PEDIDO</h6>
                            <p class="mb-1"><strong>Pedido Nº:</strong> <?php echo $pedido->id; ?></p>
                            <p class="mb-1"><strong>Mesa:</strong> <?php echo htmlspecialchars($pedido->numeroMesa); ?></p>
                            <p class="mb-1"><strong>Mozo:</strong> <?php echo htmlspecialchars($pedido->nombreMozo); ?></p>
                            <p class="mb-1"><strong>Fecha:</strong> <?php echo htmlspecialchars($factura->fechaEmision); ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-2">ESTADO</h6>
                            <span class="badge <?php echo $badgeEstado; ?> fs-5 px-4 py-2">
                                <?php echo htmlspecialchars($factura->estado); ?>
                            </span>
                        </div>
                    </div>

                    <hr>

                    <!-- DETALLE DE PRODUCTOS -->
                    <h6 class="text-muted mb-3">DETALLE DEL PEDIDO</h6>
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedido->detalles as $detalle): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detalle->nombreProducto); ?></td>
                                    <td class="text-center"><?php echo $detalle->cantidad; ?></td>
                                    <td class="text-end">$<?php echo number_format($detalle->precioUnitario, 2, ',', '.'); ?></td>
                                    <td class="text-end">$<?php echo htmlspecialchars($detalle->subtotal); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- TOTALES -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">$<?php echo htmlspecialchars($factura->subtotal); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>IVA (21%):</strong></td>
                                    <td class="text-end">$<?php echo htmlspecialchars($factura->impuestos); ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td class="text-end"><strong class="fs-5">TOTAL:</strong></td>
                                    <td class="text-end"><strong class="fs-4 text-success">$<?php echo htmlspecialchars($factura->total); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- FORMULARIO DE PAGO (solo si está pendiente) -->
                    <?php if ($factura->estado === 'PENDIENTE'): ?>
                        <hr class="my-4">
                        <div class="bg-light p-4 rounded">
                            <h5 class="mb-3">
                                <i class="fas fa-credit-card"></i> Procesar Pago
                            </h5>
                            <form method="POST" action="<?= APP_BASE_URL ?>factura/pagar">
                                <input type="hidden" name="factura_id" value="<?php echo $factura->id; ?>">
                                
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="metodo_pago" class="form-label">Método de Pago *</label>
                                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                            <option value="">-- Seleccionar --</option>
                                            <option value="efectivo">💵 Efectivo</option>
                                            <option value="tarjeta_debito">💳 Tarjeta de Débito</option>
                                            <option value="tarjeta_credito">💳 Tarjeta de Crédito</option>
                                            <option value="transferencia">🏦 Transferencia</option>
                                            <option value="mercado_pago">📱 Mercado Pago</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end mb-3">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check"></i> Confirmar Pago
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- PAGO YA PROCESADO -->
                        <hr class="my-4">
                        <div class="alert alert-success">
                            <h5 class="mb-2">
                                <i class="fas fa-check-circle"></i> Pago Procesado
                            </h5>
                            <p class="mb-0">
                                <strong>Método de pago:</strong> 
                                <?php 
                                $metodosPago = [
                                    'EFECTIVO' => '💵 Efectivo',
                                    'TARJETA_DEBITO' => '💳 Tarjeta de Débito',
                                    'TARJETA_CREDITO' => '💳 Tarjeta de Crédito',
                                    'TRANSFERENCIA' => '🏦 Transferencia',
                                    'MERCADO_PAGO' => '📱 Mercado Pago'
                                ];
                                echo $metodosPago[$factura->metodoPago] ?? $factura->metodoPago;
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PIE DE PÁGINA -->
                <div class="card-footer text-center text-muted">
                    <small>Gracias por su preferencia | Restaurante © <?php echo date('Y'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>