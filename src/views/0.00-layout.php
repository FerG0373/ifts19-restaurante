<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Restaurante</title>
    <!-- CSS -->
    <link rel="stylesheet" href="<?= APP_BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= APP_BASE_URL ?>assets/css/mesas.css">
    <!-- BOOTSTRAP -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= APP_BASE_URL ?>assets/img/ifts19_logo.png" >
</head>
<body class="bg-light">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>