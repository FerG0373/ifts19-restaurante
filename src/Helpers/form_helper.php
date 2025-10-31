<?php
//Función auxiliar para obtener el valor precargado del array $datos.
// El DTO/Mapper convierte bool a string '1'/'0' para que esto funcione.
function get_value(array $datos, string $clave): string {
    if ($clave === 'activo') {
        return htmlspecialchars($datos[$clave] ?? '1'); 
    }
    return htmlspecialchars($datos[$clave] ?? '');
}

// Inicializa las variables por defecto para el formulario.
function init_form_variables(&$datos, &$error, &$esEdicion) {
    $datos = $datos ?? []; 
    $error = $error ?? null; 
    $esEdicion = $esEdicion ?? false;
}
?>