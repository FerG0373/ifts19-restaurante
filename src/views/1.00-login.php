<div class="d-flex justify-content-center mt-5 vh-100 bg-login">
    <div class="col-md-4 mt-5">
        <form action="<?= APP_BASE_URL ?>login/procesar" method="POST" class="border p-4 rounded shadow bg-light bg-opacity-75 mt-3">
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger autoclose-alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="username" class="form-label">Usuario:</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Usuario" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Contraseña:</label>
                <input type="password" name="pass" id="pass" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary mt-3">Iniciar Sesión</button>
            </div>
            <div class="mt-5 text-center">
                <a href="<?php echo APP_BASE_URL; ?>recuperar-password" class="text-secondary text-decoration-none">Olvidé mi contraseña</a>
            </div>
        </form>        
    </div>    
</div>