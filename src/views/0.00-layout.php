<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Restaurante</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../public/assets/img/ifts19_logo.png" >
</head>
<body>
    <header>
        <?php
        require_once __DIR__ . '/_partials/_header.php';
        ?>
    </header>
    <main>
        <?php
        require_once $contenidoPrincipal;
        ?> 
    </main>
    <footer>

    </footer>
</body>
</html>