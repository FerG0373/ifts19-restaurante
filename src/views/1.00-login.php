<div class="d-flex justify-content-center mt-5 vh-100">
    <div class="col-md-4 mt-5">
        <form action="" method="POST" class="border p-4 rounded shadow bg-light bg-opacity-75 mt-5">
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            <div class="mb-3">
                <label for="username" class="form-label">Usuario:</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Contraseña: </label>
                <input type="password" name="pass" id="pass" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>


<style>
.bg-login {
    background-image: url('../public/assets/img/backgroud-login.png');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
}
</style>