<?php
// Incluir helper de formularios
require_once __DIR__ . '/../helpers/form_helper.php';

// Inicializar variables usando el helper
if (!isset($datos) || !is_array($datos)) {
    $datos = [];
}
init_form_variables($datos, $error, $esEdicion);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo); ?></h2>
        <a href="<?=APP_BASE_URL?>producto" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle"></i> **Error de <?php echo $esEdicion ? 'q   Edición' : 'Alta'; ?>:** <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" 
          action="<?= APP_BASE_URL ?>producto/<?= $esEdicion ? 'formulario/editar' : 'formulario/alta' ?>" 
          class="p-4 border rounded shadow-sm">
        
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= get_value($datos, 'id') ?>">
        <?php endif; ?>
        
        <h5 class="text-secondary mb-3">Información del Producto</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre del Producto *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= get_value($datos, 'nombre') ?>" required>
            </div>
            
            <div class="col-md-6">
                <label for="categoria" class="form-label">Categoría *</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                        $categoriaSeleccionada = get_value($datos, 'categoria');
                        $categoriasDisponibles = ['ENTRADA', 'PRINCIPAL', 'GUARNICION', 'BEBIDA', 'POSTRE'];
                        foreach ($categoriasDisponibles as $cat): 
                    ?>
                        <option value="<?= strtolower($cat) ?>" <?= (strtolower($cat) == $categoriaSeleccionada) ? 'selected' : '' ?>> 
                            <?= $cat ?> 
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción opcional del producto"><?= get_value($datos, 'descripcion') ?></textarea>
            </div>
        </div>

        <h5 class="text-secondary mb-3">Precio y Stock</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="precio" class="form-label">Precio *</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="precio" name="precio" 
                           value="<?= get_value($datos, 'precio') ?>" 
                           step="0.01" min="0" required>
                </div>
                <small class="text-muted">Ejemplo: 1250.50</small>
            </div>
            
            <div class="col-md-6">
                <label for="cantidad_stock" class="form-label">Cantidad en Stock *</label>
                <input type="number" class="form-control" id="cantidad_stock" name="cantidad_stock" 
                       value="<?= get_value($datos, 'cantidad_stock') ?>" 
                       min="0" required>
                <small class="text-muted">Unidades disponibles</small>
            </div>

            <?php if ($esEdicion): ?>
                <div class="col-md-6">
                    <label for="activo" class="form-label">Estado *</label>
                    <select class="form-select" id="activo" name="activo" required>
                        <?php 
                            $activoSeleccionado = get_value($datos, 'activo');
                        ?>
                        <option value="1" <?= ('1' === $activoSeleccionado || $activoSeleccionado === true) ? 'selected' : '' ?>>ACTIVO</option>
                        <option value="0" <?= ('0' === $activoSeleccionado || $activoSeleccionado === false) ? 'selected' : '' ?>>INACTIVO</option>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Dar de Alta' ?>
        </button>
    </form>
</div>