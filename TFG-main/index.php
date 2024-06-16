<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelNosMatao</title>
    <link rel="stylesheet" href="estilos/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Asegúrate de que esta línea esté presente -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>


</head>
<body class="bg-verde">
    <header class="header">
        <nav>     
            <input type="checkbox" id="toggle">
            <div class="logoA">
                <a href="index.php"><img src="img/logo.jpg" alt="Logo"></a>
            </div>
            
            <ul class="list">
            <li><a href="layouts/sorteo.html">Sorteco sencillo</a></li>
                <li><a href="../sensor.html">¿Cómo esta la pista?</a></li>
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
            <?php
                session_start();
                ob_start(); // La salida se guarda en el bufer

                require_once('app/AccesoDatos.php');
                require_once('app/funciones.php');
                require_once('pojos/Usuarios.php');
                require_once('pojos/Pozo.php');
                require_once('pojos/Clubs.php');
                require_once('pojos/Pozo_club.php');
                require_once('mail/send.php');
                


                if (isset($_POST['Login'])){
                    switch($_POST['Login']) {
                     //LogIn
                        case "Iniciar sesión": 
                            accionLogIn($_POST['usuario'],$_POST['contraseña']); 
                            break;
                            
                        case "Registrarse":
                            $_SESSION['checkLogin']=5;
                            //header("Location: index.php");
                            break;
                        case "forgotpsw":
                            //include_once "layouts/olvidecontr.php";
                            $_SESSION['checkLogin']=6;
                            break;
                            
                        case "Crear Usuario":
                            crudPostAlta();
                            $_SESSION['checkLogin']=4;
                            break;  
                        case "newpsw":
                            SolicitarPsw();
                            $_SESSION['checkLogin']=4;
                            break; 

                        case "Volver" || "Ya tengo cuenta" :
                            $_SESSION['checkLogin']=4;
                            header("Location: index.php");
                            break;

                    }
                }
                
                if(!isset($_SESSION['checkLogin']) || $_SESSION['checkLogin']==4){
                    //creo sesion checkLogin para que el usuario no pueda acceder sin iniciar sesion
                    //SESION CERRADA O NO INICADA
                    $_SESSION['numError']=0;
                    include_once "layouts/login.php";} 
                else if($_SESSION['checkLogin']==5){
                    //REGISTRO
                    include_once 'layouts/registro.php';
                }
                else if($_SESSION['checkLogin']==6){
                    //OLVIDE CONTRASEÑA
                    include_once 'layouts/olvidecontr.php';
                
                }else if($_SESSION['checkLogin']==2){
                    //LOGIN INCORRECTO
                    //echo "<script>alert('Usuario o contraseñas incorrectos')</script>";
                    $_SESSION['numError']=$_SESSION['numError']+1;
                    if($_SESSION['numError']>=3)echo "<script>alert('BLOQUEADO. Debe reiniciar el navegador para intentarlo de nuevo')</script>";
                    include_once "layouts/login.php";
                    
                }
                else if($_SESSION['checkLogin']==1){
                    //LOGIN CORRECTO
                        
                    if ( isset($_POST['orden'])) {
                        switch ( $_POST['orden']) {

                            case "Crear Pozo":
                                postAltaPozo();
                                $db = AccesoDatos::getModelo();
                                $tvalores = $db->getPozos();
                                header("Location: index.php");
                                break;

                            case "Apuntarse":
                                echo "<script>console.log('Id usuario: ".$_SESSION['idUser']." id pozo: ".$_SESSION['idPozo']." Posicion: ".$_POST['options']."')</script>";
                                addplayerspozo($_POST['options']);
                                break;
                            
                            case "Añadir club":
                                include_once 'layouts/creaclub.php';
                                break;
                            
                            case "crea club":
                                postAltaClub();
                                break;
                            
                            case "Modificar":
                                Update_Clubs();
                                break;
                            case "Modificar_pozo":
                                Update_pozo();
                                $idclub=$_GET['idpozo'];
                                break;
                            case "sorteo":
                                $db = AccesoDatos::getModelo();
                                $db->Sorteo($_SESSION['idPozo']);
                                getPozo($_SESSION['idPozo']);
                                break;

                            case "usuario":
                                $db = AccesoDatos::getModelo();
                                $_SESSION['User'] = $db->getUsuario($_SESSION['idUser']); 
                                header('Location: layouts/usuario.php');
                                exit;
                                break;
                            case "modificar_usr":
                                crudUsuarioModificar();
                                $db = AccesoDatos::getModelo();
                                $_SESSION['User'] = $db->getUsuario($_SESSION['idUser']); 
                                $mensaje = $_SESSION['mensajeModUS'];
                                echo "<script>
                                    alert('$mensaje');
                                    window.location.href = 'layouts/usuario.php';
                                </script>";
                                break;

                            }
                           
                            
                    }

                    if ( isset($_GET['orden'])) {
                        switch ( $_GET['orden']) {

                            case "detallespozo":
                            getPozo($_GET['id']);
                            break;

                            case "detallesclub":
                            $_SESSION["idclub"]=$_GET['id'];
                            //echo "<script>console.log('hola')</script>";
                            getClub($_GET['id']);
                            break;

                            case "desapuntarpozo":
                                //echo "<script>console.log('hola')</script>";
                                $idpozo=$_GET['idpozo'];$IdUser=$_SESSION['idUser'];
                                Delete_pozo_usuario($idpozo,$IdUser);
                                echo "<script>document.location.href='?orden=detallespozo&id='+$idpozo;</script>";
                                break;
                            
                            case "filtro_lista":
                                $estado = $_GET['estado'];
                                getPozos($estado);
                                break;

                            case "Nuevo pozo":
                                cargarSelectClubs();
                                break;
                            
                            case "Clubs":
                                mostrarClubs();
                                break;   
        
                                    
                            case "Cerrar Sesion":
                                $_SESSION['checkLogin']=4;
                                header("Location: index.php");
                                break;

                        }
                }

                if ( ob_get_length() == 0){
                    $db = AccesoDatos::getModelo();
                    $tvalores = $db->getPozos();
                    include_once 'layouts/lista_pozos.php';   
                }
                     
            }   
            ob_end_flush(); // Libera el buffer de salida   
            ?>
        </div>

        
    <footer>
        <img src="img/logo.jpg" alt="" class="logo">
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