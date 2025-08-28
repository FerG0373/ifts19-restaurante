<nav>
    <?php
    foreach ($rutas as $url => $ruta_archivo) {
        $nombre_enlace = ucfirst($url);
        echo "<a href='$url'>$nombre_enlace</a>";
    }
    ?>
</nav>