<div class="container my-5">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><?php echo htmlspecialchars($titulo ?? 'Error'); ?></h4>
        <p class="mb-0">
            <!-- Muestra el mensaje de la excepción que viene del Controller -->
            <?php echo htmlspecialchars($mensaje ?? 'Ocurrió un error desconocido en la aplicación.'); ?>
        </p>
        <hr>
        <p class="mb-0 small">Si el error persiste, contacte al administrador del sistema.</p>
    </div>
</div>
