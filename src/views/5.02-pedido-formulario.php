<<?php
use App\Models\Producto;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo); ?></h2>
        <a href="<?= APP_BASE_URL ?>pedido" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_BASE_URL ?>pedido/formulario/alta" class="p-4 border rounded shadow-sm bg-white">
        
        <h5 class="text-secondary mb-3">Información del Pedido</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="tipo_pedido" class="form-label">Tipo de Pedido *</label>
                <select class="form-select" id="tipo_pedido" name="tipo_pedido" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="mesa" selected>Mesa</option>
                    <option value="domicilio">Domicilio</option>
                    <option value="llevar">Para Llevar</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="mesa_id" class="form-label">Mesa *</label>
                <select class="form-select" id="mesa_id" name="mesa_id" required>
                    <option value="">-- Seleccionar Mesa --</option>
                    <?php foreach ($mesas as $mesa): ?>
                        <option value="<?= $mesa['id'] ?>">
                            Mesa <?= htmlspecialchars($mesa['numero_mesa']) ?> 
                            (<?= htmlspecialchars($mesa['ubicacion']) ?> - <?= $mesa['capacidad'] ?> personas)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="personal_id" class="form-label">Mozo *</label>
                <input type="number" class="form-control" id="personal_id" name="personal_id" 
                       value="<?= $_SESSION['usuario_id'] ?? 1 ?>" required readonly>
                <small class="text-muted">ID del mozo actual</small>
            </div>

            <div class="col-12">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" 
                          placeholder="Observaciones generales del pedido (opcional)"></textarea>
            </div>
        </div>

        <hr>

        <h5 class="text-secondary mb-3">Productos del Pedido</h5>
        
        <div id="productos-container">
            <!-- Fila de producto inicial -->
            <div class="producto-item row g-3 mb-3 border-bottom pb-3">
                <div class="col-md-5">
                    <label class="form-label">Producto *</label>
                    <select class="form-select producto-select" name="productos[0][producto_id]" required>
                        <option value="">-- Seleccionar Producto --</option>
                        <?php 
                        /** @var Producto $producto */
                        foreach ($productos as $producto): 
                        ?>
                            <option value="<?= $producto->getId() ?>" 
                                    data-precio="<?= $producto->getPrecio() ?>"
                                    data-stock="<?= $producto->getCantidadStock() ?>">
                                <?= htmlspecialchars($producto->getNombre()) ?> 
                                - $<?= number_format($producto->getPrecio(), 2, ',', '.') ?>
                                (Stock: <?= $producto->getCantidadStock() ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Cantidad *</label>
                    <input type="number" class="form-control cantidad-input" 
                           name="productos[0][cantidad]" min="1" value="1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Instrucciones</label>
                    <input type="text" class="form-control" name="productos[0][instrucciones]" 
                           placeholder="Ej: Sin cebolla">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-producto w-100" disabled>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="button" id="agregar-producto" class="btn btn-success btn-sm mb-4">
            <i class="fas fa-plus"></i> Agregar Otro Producto
        </button>

        <hr>

        <div class="d-flex justify-content-between align-items-center">
            <h4 class="text-success mb-0">
                Total Estimado: $<span id="total-estimado">0.00</span>
            </h4>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Crear Pedido
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productoIndex = 1;
    const container = document.getElementById('productos-container');
    const btnAgregar = document.getElementById('agregar-producto');
    
    // Función para calcular el total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.producto-item').forEach(item => {
            const select = item.querySelector('.producto-select');
            const cantidad = item.querySelector('.cantidad-input').value || 0;
            const precio = select.options[select.selectedIndex]?.dataset.precio || 0;
            total += parseFloat(precio) * parseInt(cantidad);
        });
        document.getElementById('total-estimado').textContent = total.toFixed(2).replace('.', ',');
    }

    // Agregar nuevo producto
    btnAgregar.addEventListener('click', function() {
        const nuevoProducto = container.querySelector('.producto-item').cloneNode(true);
        
        // Actualizar índices
        nuevoProducto.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${productoIndex}]`);
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else if (input.type === 'number') {
                input.value = 1;
            } else {
                input.value = '';
            }
        });
        
        // Habilitar botón eliminar
        nuevoProducto.querySelector('.remove-producto').disabled = false;
        
        container.appendChild(nuevoProducto);
        productoIndex++;
        
        // Recalcular total
        calcularTotal();
    });

    // Eliminar producto
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-producto')) {
            if (container.querySelectorAll('.producto-item').length > 1) {
                e.target.closest('.producto-item').remove();
                calcularTotal();
            }
        }
    });

    // Actualizar total cuando cambian productos o cantidades
    container.addEventListener('change', calcularTotal);
    container.addEventListener('input', calcularTotal);
    
    // Calcular total inicial
    calcularTotal();
});
</script>