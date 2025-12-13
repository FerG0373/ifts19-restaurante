<div class="d-flex justify-content-center mt-5 vh-100 bg-login">
    <div class="col-md-4 mt-5">
        <form action="<?= APP_BASE_URL ?>recuperar-password/procesar" method="POST" class="border p-4 rounded shadow bg-light bg-opacity-75 mt-3">
            <h2 class="text-center mb-4">Nueva Contraseña</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger autoclose-alert"><?= htmlspecialchars($error) ?></div>
            <?php elseif (isset($exito)): ?>
                <div class="alert alert-success autoclose-alert"><?= htmlspecialchars($exito) ?></div>
            <?php endif; ?>            

            <div class="mb-3">
                <label for="username" class="form-label">Usuario:</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Ingresá tu número de DNI" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="nueva_pass" class="form-label">Nueva contraseña:</label>
                <input type="password" name="nueva_pass" id="nueva_pass" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirmar_pass" class="form-label">Confirmar contraseña:</label>
                <input type="password" name="confirmar_pass" id="confirmar_pass" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary mt-3">Restablecer Contraseña</button>
            </div>
            <div class="mt-4 text-center">
                <a href="<?php echo APP_BASE_URL; ?>login" class="text-secondary text-decoration-none"><i class="fas fa-chevron-left"></i> Volver al Login</a>
            </div>
        </form>        
    </div>    
</div>