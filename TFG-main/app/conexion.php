<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexión</title>
</head>
<body>
    <?php
        $enlace = mysqli_connect("localhost","root","root","pozo");

        if(!$enlace){
           die("No pudo conectarse a la bbdd".mysqli_error());
        }
        echo "Conexión exitosa";
        mysqli_close($enlace);
    ?>
</body>
</html>