<?php

function accionLogIn($user,$psw){
    $db = AccesoDatos::getModelo();
    $checkusuario=$db->checkUsuario($user);
    if($checkusuario==false){      
        $_SESSION['checkLogin']=2;
        echo "<script>alert('No existe el usuario')</script>";
        include_once 'layouts/login.php';
    }else{
        //if(password_verify($psw, $checkusuario->contraseña)==1){
        if($psw == $checkusuario->contraseña){
          $_SESSION['checkLogin']=1;
            $_SESSION['idUser']=$checkusuario->Id;  
        }
        else{
            $_SESSION['checkLogin']=2;
         echo "<script>alert('Contraseña incorrecta')</script>";
            include_once 'layouts/login.php';
        }
        
    }   
}

function crudPostAlta() {
    $db = AccesoDatos::getModelo();
    $usr = new Usuarios();
    $usr->Id = $_SESSION['idUser'];
    $usr->Nombre = $_POST['nombre'];
    $usr->Apellidos = $_POST['apellidos'];
    $usr->Apodo = $_POST['alias'];
    //$usr->contraseña = password_hash($_POST['contrasenia'], PASSWORD_BCRYPT); // contraseña cifrada
    $usr->contraseña = $_POST['contrasenia'];
    $usr->CORREO = $_POST['correo'];
    
    $mensaje = $db->addUsuario($usr, 1);
    echo "<script>alert('$mensaje')</script>";
}

function crudUsuarioModificar(){
    $db = AccesoDatos::getModelo();
    $usr = new Usuarios();
    $usr->Id = $_SESSION['idUser'];
    $usr->Nombre   = $_POST['nombre'];
    $usr->Apellidos =$_POST['apellidos'];
    $usr->Apodo=$_POST['alias'];
    $usr->contraseña = $_POST['nueva_contrasena'];
    //$usr->contraseña = password_hash($_POST['contrasenia'], PASSWORD_BCRYPT); //contraseña cifrada
    $usr->CORREO=$_POST['correo'];

    $_SESSION['mensajeModUS'] =  $db->addUsuario($usr,2);
}

function postAltaPozo(){
    $db = AccesoDatos::getModelo();
    $pozo= new Pozo();
    $pozo->nombre_club=$_POST['club'];
    $pozo->fecha_inicio_inscripcion=date('Y-m-d');
    $pozo->fecha_fin_inscripcion=$_POST['ffinincs'];
    $pozo->precio=$_POST['precio'];
    $pozo->numero_jugadores_max=$_POST['njmax'];
    $pozo->numero_jugadores_actuales=0;
    $pozo->fecha_pozo=$_POST['fpozo'];
    $pozo->Id_club=$_POST['club'];
    $pozo->ESTADO=1;
    $pozo->ID_CREADOR=$_SESSION['idUser'];

    if ( $db->addPozo($pozo) ) {
        echo "<script>alert(' El pozo ".$pozo->nombre_club." se ha creado con exito')</script>";
    } else {
        echo "<script>alert('Error al crear el pozo ".$pozo->nombre_club.".')</script>";
    }
}

function postAltaClub(){
    $db = AccesoDatos::getModelo();
    $club= new Clubs();
    $club->Nombre=$_POST['nombre'];
    $club->Localidad=$_POST['localidad'];
    $club->ccpp=$_POST['ccpp'];
    $club->Direccion=$_POST['dir'];
    $club->web=$_POST['web'];
    $club->Link_maps=$_POST['lmaps'];
    $club->Observaciones=$_POST['obs'];
    $club->Latitud=$_POST['latitud'];
    $club->Longitud=$_POST['longitud'];

    if ( $db->addClub($club) == 1 ) {
        echo "<script>alert(' El club ".$club->Nombre." se ha añadido con exito')</script>";
        cargarSelectClubs();

    }
}

function getPozo($id){
    $db = AccesoDatos::getModelo();
    $pozo = $db->getPozo($id);
    $info = $db->getPozoRestriccion($id);
    $sorteo = $db->getSorteoPozo($id);
    $jugadores_pozo = $db->getJugadores($id);
    include_once "layouts/detallespozo.php";
}

function getPozos($estado){
    $db = AccesoDatos::getModelo();
    
    $tvalores = $db->getPozosFiltro($estado);
    include_once 'layouts/lista_pozos.php'; 
}

function getClub($id){
    $db = AccesoDatos::getModelo();
    $club = $db->getClub($id);
    //echo "<script>console.log('hola, pepe')</script>";
    include_once "layouts/detallesclub.php";
}

 function addplayerspozo($posicion){
     $db = AccesoDatos::getModelo();
        $Usuario_pozo = new Usuario_pozo();
        $Usuario_pozo->id_usuario = $_SESSION['idUser'];
        $Usuario_pozo->posicion = $posicion;
        $Usuario_pozo->id_pozo = $_SESSION['idPozo'];
        
        $mensaje = $db->añadirjugpozo($Usuario_pozo);
        echo "<script>console.log('$mensaje')</script>";
        if($mensaje == 'Te acabas de inscribir al pozo.') {
            $mensajeEstado = $db->cambiarEstado($_SESSION['idPozo']); 
            echo "<script>console.log('$mensajeEstado')</script>";
        }
        
        echo "<script>alert('$mensaje')</script>";
 }

 function cargarSelectClubs(){
    $db = AccesoDatos::getModelo();
    $clubs = $db->getClubs();
    include_once 'layouts/creapozo.php';
 }

function mostrarClubs(){
    $db = AccesoDatos::getModelo();
    $clubs = $db->getClubs();
    include_once 'layouts/lista_clubs.php';
}

function Delete_pozo_usuario($id_pozo, $id_usuario) {
    $db = AccesoDatos::getModelo();
    $mensaje = $db->deleteJugPozo($_SESSION['idUser'], $_SESSION['idPozo']);

    if($mensaje == 'Has sido borrado del pozo.') {
        $mensajeEstado = $db->cambiarEstado($_SESSION['idPozo']); 
        echo "<script>console.log('$mensajeEstado')</script>";
    }
    
    echo "<script>alert('$mensaje');</script>";
}

function Update_Clubs(){
    $db = AccesoDatos::getModelo();
    $club= new Clubs();
    $club->Nombre=$_POST['txtNombre'];
    $club->Localidad=$_POST['txtLocalidad'];
    $club->ccpp=$_POST['txtCcpp'];
    $club->Direccion=$_POST['txtDir'];
    $club->web=$_POST['txtWeb'];
    $club->Observaciones=$_POST['txtObsl'];
    $club->Latitud=$_SESSION['latitud'];
    $club->Longitud=$_SESSION['longitud'];

    if ( $db->ModificarClub($club, $_SESSION['ID_club']) ) {
        echo "<script>alert(' El club ".$club->Nombre." se ha modificado con exito')</script>";
        

    } else {
        echo "<script>alert('Error al modificar el club ".$club->Nombre.".')</script>";
    }
}

function Update_pozo(){
    $db = AccesoDatos::getModelo();
    $pozo= new Pozo();
    $pozo->id=$_SESSION['idPozo'];
    $pozo->precio=$_POST['precio'];
    $pozo->fecha_fin_inscripcion=$_POST['ffinincs'];
    $pozo->fecha_pozo=$_POST['fpozo'];
    $pozo->numero_jugadores_max=$_POST['njmax'];
    $mensaje = $db->UpdatePozo($pozo);
    echo "<script>alert('$mensaje');</script>";
}

function SolicitarPsw() {
    $db = AccesoDatos::getModelo();  
    $result = $db->NuevaContraseña($_POST['nombre'], $_POST['alias'],$_POST['correo']);
    
    if (isset($result['mensaje'])) {
        if (isset($result['nueva_contraseña'])) {
            // Guardar la contraseña en una variable
            $newPassword = $result['nueva_contraseña'];
            correo($_POST['correo'], 'Nueva contraseña', htmlpsw($_POST['nombre'], $newPassword));
        }
        // Mostrar solo el mensaje
        echo "<script>alert('{$result['mensaje']}')</script>";
    } else {
        echo "<script>alert('Error: No se pudo procesar la solicitud.')</script>";
    }
}
