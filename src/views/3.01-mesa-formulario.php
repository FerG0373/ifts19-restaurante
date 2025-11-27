<?php
// Variables disponibles desde el Controller:
// $titulo, $ubicaciones (array de strings: ['salon', 'exterior', ...]), 
// $datos (datos precargados en caso de error o edición), $error (mensaje de error)
// $esEdicion (boolean, true si estamos editando una mesa existente)

// Incluir helper de formularios
require_once __DIR__ . '/../helpers/form_helper.php';

// Inicializar variables usando el helper
// Nota: Aquí asumimos que $datos, $error y $esEdicion están disponibles en el scope
// o que se obtienen de $_SESSION en el helper, similar a cómo lo hace Personal.
init_form_variables($datos, $error, $esEdicion); // Ajustar según tu form_helper

// Las ubicaciones deben venir del Controller, pero proveemos un fallback.
$ubicaciones = $ubicaciones ?? ['salon', 'exterior', 'barra'];
$datos = $datos ?? [];
$rutaBase = APP_BASE_URL . 'mesas/';

// Definir la ruta de acción del formulario:
$accionForm = $rutaBase . ($esEdicion ? 'formulario/editar' : 'formulario/alta');

?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><?php echo htmlspecialchars($titulo); ?></h2>
        <a href="<?= $rutaBase ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Tablero
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle"></i> **Error de <?php echo $esEdicion ? 'Edición' : 'Alta'; ?>:** <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" 
          action="<?= $accionForm ?>" 
          class="p-4 border rounded shadow-sm">
        
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= get_value($datos, 'id') ?>">
        <?php endif; ?>
        
        <h5 class="text-secondary mb-3">Datos de la Mesa</h5>
        <div class="row g-3 mb-4">
            
            <div class="col-md-6">
                <label for="nroMesa" class="form-label">Número / Identificador</label>
                <input type="text" class="form-control" id="nroMesa" name="nroMesa" 
                       value="<?= get_value($datos, 'nroMesa') ?>" required>
            </div>
            
            <div class="col-md-6">
                <label for="capacidad" class="form-label">Capacidad (Personas)</label>
                <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" 
                       value="<?= get_value($datos, 'capacidad') ?>" required>
            </div>
            
            <div class="col-md-6">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <select class="form-select" id="ubicacion" name="ubicacion" required>
                    <option value="">-- Seleccionar --</option>
                    <?php
                        $ubicacionSeleccionada = get_value($datos, 'ubicacion');
                        foreach ($ubicaciones as $ubicacionDB): 
                            // Convertir a mayúsculas para la etiqueta
                            $etiqueta = strtoupper($ubicacionDB);
                            // Las ubicaciones de la DB ('salon', 'exterior', etc.) deben ser el valor
                    ?>
                        <option value="<?= $ubicacionDB ?>" <?= ($ubicacionDB == $ubicacionSeleccionada) ? 'selected' : '' ?>>
                            <?= $etiqueta ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($esEdicion): ?>
                <div class="col-md-6">
                    <label for="estadoMesa" class="form-label">Estado Operativo</label>
                    <select class="form-select" id="estadoMesa" name="estadoMesa" required>
                        <?php 
                            $estadoSeleccionado = get_value($datos, 'estadoMesa');
                            // Asume que tienes un Enum o array de estados disponibles para iterar
                            // Ejemplo: ['libre', 'ocupada', 'reservada', 'inhabilitada']
                            $estadosDisponibles = ['libre', 'ocupada', 'reservada', 'inhabilitada'];
                            foreach ($estadosDisponibles as $estadoDB):
                        ?>
                            <option value="<?= $estadoDB ?>" <?= ($estadoDB === $estadoSeleccionado) ? 'selected' : '' ?>>
                                <?= strtoupper($estadoDB) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i> <?= $esEdicion ? 'Guardar Cambios' : 'Dar de Alta' ?>
        </button>
    </form>
</div>