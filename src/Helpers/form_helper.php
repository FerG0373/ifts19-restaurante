<?php
// Obtiene y prepara un valor para precargar un campo de formulario.
// Los valores de $datos provienen de un DTO::toArray(), manteniendo 'activo' como booleano.

function get_value(array $datos, string $clave): string {
    // Si la clave existe en el array de datos (Modo Edición).
    if (isset($datos[$clave])) {
        $valor = $datos[$clave];        
        // Manejo especial para 'activo', convertimos el booleano (true/false) a la cadena ('1'/'0').
        if ($clave === 'activo') {
            return (string)(int)(bool)$valor;
        }        
        // Para todas las demás claves se asegura que sea string y escapa HTML.
        return htmlspecialchars((string)$valor);
    }     
    // Si la clave no está presente (ej. formulario de alta)
    if ($clave === 'activo') {
        // En el formulario de alta, por defecto el personal está activo.
        return '1'; 
    }    
    // Para cualquier otro campo que falte, retorna una cadena vacía.
    return '';
}

// Inicializa las variables por defecto para el formulario.
function init_form_variables(&$datos, &$error, &$esEdicion) {
    $datos = $datos ?? []; 
    $error = $error ?? null; 
    $esEdicion = $esEdicion ?? false;
}
?>