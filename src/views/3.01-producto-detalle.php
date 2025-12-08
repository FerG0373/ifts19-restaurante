<?php
use App\DTOs\ProductoVistaDTO;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">        
        <div class="d-flex align-items-center">            
            <h2 class="text-primary mb-0 me-3"><?php echo htmlspecialchars($titulo); ?></h2>                        
            <form action="<?= APP_BASE_URL ?>producto/formulario/cargar" method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?= htmlspecialchars($producto->id); ?>">
                <button type="submit" class="btn btn-link fa-lg mt-2" title="Editar Producto">
                    <i class="fas fa-edit"></i>
                </button>
            <form action="<?= APP_BASE_URL ?>producto/formulario/cargar" method="POST"> 
        </div>
        <a href="<?=APP_BASE_URL?>producto" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <?php
    /** @var ProductoVistaDTO $producto */
    if (empty($producto)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje ?? 'No se encontró el producto.'); ?></div>
    <?php else: ?>
        <div class="card shadow-sm mx-auto mt-5" style="max-width: 600px;">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Información del Producto</h4>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    
                    <dt class="col-sm-4 text-nowrap">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($producto->id); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Nombre:</dt>
                    <dd class="col-sm-8 fw-bold"><?php echo htmlspecialchars($producto->nombre); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Descripción:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($producto->descripcion ?: 'Sin descripción'); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Categoría:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($producto->categoria); ?></span>
                    </dd>
                    
                    <dt class="col-sm-4 text-nowrap">Precio:</dt>
                    <dd class="col-sm-8 text-success fw-bold fs-5">$<?php echo htmlspecialchars($producto->precio); ?></dd>
                    
                    <dt class="col-sm-4 text-nowrap">Stock Disponible:</dt>
                    <dd class="col-sm-8">
                        <?php if ($producto->cantidadStock > 10): ?>
                            <span class="badge bg-success fs-6"><?php echo $producto->cantidadStock; ?> unidades</span>
                        <?php elseif ($producto->cantidadStock > 0): ?>
                            <span class="badge bg-warning fs-6"><?php echo $producto->cantidadStock; ?> unidades</span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6">Sin stock</span>
                        <?php endif; ?>
                    </dd>
                    
                    <dt class="col-sm-4 text-nowrap">Estado:</dt>
                    <dd class="col-sm-8">
                        <?php if ($producto->activo): ?>
                            <span class="text-success fw-bold">Activo</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Inactivo</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    <?php endif; ?>
</div>