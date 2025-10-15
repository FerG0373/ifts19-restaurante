<?php
// Usamos APP_BASE_URL para construir la URL de acción si es necesario
// Asumimos que los datos y el error vienen del controlador en caso de fallo.
$datos = $datos ?? []; // Inicializamos $datos si no existen (primer acceso)
$error = $error ?? null; // Inicializamos $error si no existe

// Función auxiliar para obtener el valor precargado del array $datos
function get_value(array $data, string $key): string {
    return htmlspecialchars($data[$key] ?? '');
}
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
            <i class="fas fa-exclamation-triangle"></i> **Error de Alta:** <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="personal/formulario/alta" class="p-4 border rounded shadow-sm">
        
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
                <input type="text" class="form-control" id="dni" name="dni" value="<?= get_value($datos, 'dni') ?>" required>
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
                    <option value="">Seleccione...</option>
                    <?php
                        $puestoSeleccionado = get_value($datos, 'puesto');
                        $puestosDisponibles = ['ENCARGADO', 'COCINERO', 'MOZO', 'CAJERO', 'BARTENDER'];
                        foreach ($puestosDisponibles as $p): 
                    ?>
                        <option value="<?= $p ?>" <?= ($p == $puestoSeleccionado) ? 'selected' : '' ?>> <?= $p ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="col-md-6">
                <label for="sexo" class="form-label">Sexo</label>
                <select class="form-select" id="sexo" name="sexo" required>
                    <option value="">Seleccione...</option>
                    <?php 
                        $sexoSeleccionado = get_value($datos, 'sexo');
                        $sexosDisponibles = ['M', 'F', 'X'];
                        foreach ($sexosDisponibles as $s): 
                    ?>
                        <option value="<?= $s ?>" <?= ($s == $sexoSeleccionado) ? 'selected' : '' ?>> <?= $s ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <h5 class="text-secondary mb-3">Seguridad</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="pass" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="pass" name="pass" required>
            </div>
            <div class="col-md-6">
                <label for="pass_confirmacion" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="pass_confirmacion" name="pass_confirmacion" required>
            </div>
            <div class="col-md-6">
                <label for="perfilAcceso" class="form-label">Perfil de Acceso</label>
                <select class="form-select" id="perfilAcceso" name="perfilAcceso" required>
                    <option value="">Seleccione...</option>
                    <?php 
                        $perfilSeleccionado = get_value($datos, 'perfilAcceso');
                        $perfilesDisponibles = ['ENCARGADO', 'MOZO'];
                        foreach ($perfilesDisponibles as $p): 
                    ?>
                        <option value="<?= $p ?>" <?= ($p == $perfilSeleccionado) ? 'selected' : '' ?>> <?= $p ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">
            <i class="fas fa-save"></i> Guardar Personal
        </button>
    </form>
</div>