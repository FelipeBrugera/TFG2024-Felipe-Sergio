<?php
session_start();

// require_once('../app/AccesoDatos.php');
// require_once('../app/funciones.php');
// require_once('../pojos/Usuarios.php');



if(!isset($_SESSION['checkLogin']) || $_SESSION['checkLogin']==4){
    header("Location: ../index.php");
    exit;
}else if($_SESSION['checkLogin']==1){

        //$db = AccesoDatos::getModelo();
        //$usuario = $db->getUsuario($_SESSION['idUser']); 
        //echo "<script>console.log('hola');</script>";
        if(isset($_SESSION['User'])){
            $usuario = $_SESSION['User'];
            //echo "<script>console.log('$nombre');</script>";
        }

        if ( isset($_GET['orden'])) {
        switch ( $_GET['orden']) {

        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelNosMatao</title>
    <link rel="stylesheet" href="../estilos/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body class="bg-verde">
    <header class="header">
        <nav>     
            <input type="checkbox" id="toggle">
            <div class="logoA">
                <a href="../index.php"><img src="../img/logo.jpg" alt="Logo"></a>
            </div>
            
            <ul class="list">
            <li><a href="sorteo.html">Sorteco sencillo</a></li>
                <li><a href="../../sensor.html">¿Cómo esta la pista?</a></li>
                <li><a href="https://worldpadeltour.com/noticias">Sigue el WPT</a></li>
                <li><a href="https://linktr.ee/marcoserg">Apple gratis</a></li>
                <form method="post">
                    <li><button class="btnLlegar" type="submit" name="orden" value="usuario">Usuario</button></li>
                </form>
            </ul>

            <label for="toggle" class="icon-bars">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </label>
            
    </nav>

    </header>

    <div id="content" class="content">
        <div class="contenido-usuario">
            <h2><b>Usuario: <?=$usuario['Nombre'];?></b></h2>
            <p>Pozos de derecha: <?=$usuario['cuenta_D'];?></p>
            <p>Pozos de reves: <?=$usuario['cuenta_R'];?></p>
            <form method="post" action="../index.php">
                <p>Nombre: <input type="text" name="nombre" class="input-user" value="<?=$usuario['Nombre'];?>" required></p>
                <p>Apellidos: <input type="text" name="apellidos" class="input-user" value="<?=$usuario['Apellidos'];?>" required></p>
                <p>Alias: <input type="text" name="alias" class="input-user" value="<?=$usuario['Apodo'];?>" required></p>
                <p>Correo: <input type="text" name="correo" class="input-user" value="<?=$usuario['CORREO'];?>" required></p>
                <p>Nueva contraseña: <input type="password" id="nueva_contrasena" name="nueva_contrasena"></p>
                <p>Repite la contraseña: <input type="password" id="repite_contrasena" name="repite_contrasena"></p>
                <p id="password-message" class="password-mismatch"></p>
                <div class="controls-usr">
                    <button type="submit" id="botModificar" name="orden" value="modificar_usr" class="botonsr">Modificar</button>
                    <button type="button" onclick="window.location.href='../index.php'" class="botonsr">Volver</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <img src="../img/logo.jpg" alt="" class="logo">
        <div class="social-icons-container">
            <a href="#" class="social-icon"></a>
            <a href="#" class="social-icon"></a>
            <a href="#" class="social-icon"></a>
        </div>
        <ul class="footer-menu-container">
            <li class="menu-item">Email: 0padelnosmatao@gmail.com</li>
            <li class="menu-item">Alcalá de henares</li>
            <li class="menu-item">TFG Sergio Marco. 2024</li>
        </ul>

  </footer>
</body>
</html>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const nuevaContrasena = document.getElementById('nueva_contrasena');
            const repiteContrasena = document.getElementById('repite_contrasena');
            const passwordMessage = document.getElementById('password-message');
            const botModificar = document.getElementById('botModificar');

            function checkPasswords() {
                if (nuevaContrasena.value === repiteContrasena.value || nuevaContrasena.value == "") {
                    passwordMessage.textContent = '';
                    botModificar.disabled = false;
                } else {
                    passwordMessage.textContent = 'Las contraseñas no coinciden';
                    botModificar.disabled = true;
                }
            }

            nuevaContrasena.addEventListener('input', checkPasswords);
            repiteContrasena.addEventListener('input', checkPasswords);
        });
    </script>