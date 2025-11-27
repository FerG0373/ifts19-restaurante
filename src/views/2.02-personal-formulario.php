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
        <a href="<?=APP_BASE_URL?>personal" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle"></i> **Error de <?php echo $esEdicion ? 'Edición' : 'Alta'; ?>:** <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" 
          action="<?= APP_BASE_URL ?>personal/<?= $esEdicion ? 'formulario/editar' : 'formulario/alta' ?>" 
          class="p-4 border rounded shadow-sm">
        
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= get_value($datos, 'id') ?>">
        <?php endif; ?>
        
        <h5 class="text-secondary mb-3">Datos Personales</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= get_value($datos, 'nombre') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?= get_value($datos, 'apellido') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="dni" class="form-label">DNI</label>
                <input type="text" class="form-control" id="dni" name="dni" value="<?= get_value($datos, 'dni') ?>" required <?= $esEdicion ? 'readonly' : '' ?>>
            </div>
            <div class="col-md-6">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= get_value($datos, 'fecha_nacimiento') ?>" required>
            </div>
        </div>

        <h5 class="text-secondary mb-3">Datos de Contacto y Rol</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="email" class="form-label">Email (Usuario)</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= get_value($datos, 'email') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= get_value($datos, 'telefono') ?>">
            </div>
            <div class="col-md-6">
                <label for="puesto" class="form-label">Puesto</label>
                <select class="form-select" id="puesto" name="puesto" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                        $puestoSeleccionado = get_value($datos, 'puesto');
                        $puestosDisponibles = ['ENCARGADO', 'COCINERO', 'MOZO', 'CAJERO', 'BARTENDER'];
                        foreach ($puestosDisponibles as $p): 
                    ?>
                        <option value="<?= strtolower($p) ?>" <?= (strtolower($p) == $puestoSeleccionado) ? 'selected' : '' ?>> <?= $p ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="sexo" class="form-label">Sexo</label>
                <select class="form-select" id="sexo" name="sexo" required>
                    <option value="">-- Seleccionar --</option>
                    <?php 
                        $sexoSeleccionado = get_value($datos, 'sexo');
                        $sexosDisponibles = ['m' => 'MASCULINO', 'f' => 'FEMENINO', 'x' => 'X'];
                        foreach ($sexosDisponibles as $valorDB => $etiqueta): 
                    ?>
                        <option value="<?= $valorDB ?>" <?= ($valorDB == $sexoSeleccionado) ? 'selected' : '' ?>>
                            <?= $etiqueta ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <h5 class="text-secondary mb-3">Seguridad</h5>
        <div class="row g-3 mb-4">
            
            <?php if (!$esEdicion): ?>
                <div class="col-md-6">
                    <label for="pass" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="pass" name="pass" required>
                </div>
                <div class="col-md-6">
                    <label for="pass_confirmacion" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="pass_confirmacion" name="pass_confirmacion" required>
                </div>
            <?php endif; ?>
            
            <div class="col-md-6">
                <label for="perfil_acceso" class="form-label">Perfil de Acceso</label>
                <select class="form-select" id="perfil_acceso" name="perfil_acceso" required>
                    <option value="">-- Seleccionar --</option>
                    <?php 
                        // Asegura de que el valor precargado esté en minúsculas (ej: 'encargado').
                        $perfilSeleccionado = get_value($datos, 'perfil_acceso');
                        $perfilesDisponibles = ['ENCARGADO', 'MOZO'];
                        foreach ($perfilesDisponibles as $p): 
                    ?>
                        <option value="<?= strtolower($p) ?>" <?= (strtolower($p) == $perfilSeleccionado) ? 'selected' : '' ?>> <?= $p ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($esEdicion): ?>
                <div class="col-md-6">
                    <label for="activo" class="form-label">Estado</label>
                    <select class="form-select" id="activo" name="activo" required>
                        <?php 
                            // El valor es '1' o '0' (cadenas), según la lógica del Mapper/DTO
                            $activoSeleccionado = get_value($datos, 'activo');
                        ?>
                        <option value="1" <?= ('1' === $activoSeleccionado) ? 'selected' : '' ?>>ACTIVO</option>
                        <option value="0" <?= ('0' === $activoSeleccionado) ? 'selected' : '' ?>>INACTIVO (Baja)</option>
                    </select>
                </div>
            <?php endif; ?>            
        </div>
        
        <button type="submit" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Dar de Alta' ?>
        </button>
    </form>
</div>