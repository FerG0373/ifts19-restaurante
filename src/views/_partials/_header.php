<nav>
    <?php
    foreach ($arrayRutas as $url => $rutaArchivo) {
        $nombreEnlace = ucfirst($url);
        echo "<a href='$url'>$nombreEnlace</a>";
    }
    ?>
</nav>