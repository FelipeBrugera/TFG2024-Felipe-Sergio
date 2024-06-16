<?php
//include_once "pojos/Usuarios.php";
include_once "pojos/Pozo.php";
include_once "pojos/Usuario_pozo.php";
require_once "app/config.php";

class MySQLException extends Exception {};

class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    private function __construct(){
        
        try {
            $dsn = "mysql:host=".DB_SERVER.";dbname=".DATABASE.";charset=utf8";
            // Creo el objeto PDO estableciendo la conexión a la BD
            $this->dbh = new PDO($dsn,DB_USER,DB_PASSWD);
            // Si falla genera una excepción
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }   
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh = null; 
            self::$modelo = null; // Borro el objeto.
        }
    }

    

    /*public function checkUsuario ($nombre, $clave) {
        $stmt_usuario   = $this->dbh->prepare("SELECT * FROM `usuario` WHERE apodo=:nombre and contraseña=:clave");
        $stmt_usuario->bindParam(':nombre', $nombre);
        $stmt_usuario->bindParam(':clave', $clave);
        $stmt_usuario->setFetchMode(PDO::FETCH_CLASS, 'Usuarios');
        if ( $stmt_usuario->execute() ){
            if ( $obj = $stmt_usuario->fetch()){
                return $obj;
            }else{
                return false;
            }
        }         
    }*/

    public function checkUsuario($nombre) {
        $stmt_usuario = $this->dbh->prepare("SELECT * FROM usuario WHERE apodo = :nombre");
        $stmt_usuario->bindParam(':nombre', $nombre);
        $stmt_usuario->setFetchMode(PDO::FETCH_CLASS, 'Usuarios');
    
        if ($stmt_usuario->execute()) {
            if ($obj = $stmt_usuario->fetch()) {
                    return $obj; // Las contraseñas coinciden, devolver el objeto del usuario
            } else {
                return false; // No se encontró el usuario
            }
        } 
    }
    
    


    public function addUsuario($usr, $invoker):string{
       
        // El id se define automáticamente por autoincremento.
        $stmt_crearcli  = $this->dbh->prepare(
            "CALL PA_USUARIO(?,?,?,?,?,?,?)");
        $stmt_crearcli->bindValue(1,$usr->Id);    
        $stmt_crearcli->bindValue(2,$usr->Nombre);
        $stmt_crearcli->bindValue(3,$usr->Apellidos);
        $stmt_crearcli->bindValue(4,$usr->Apodo);
        $stmt_crearcli->bindValue(5,$usr->contraseña); 
        $stmt_crearcli->bindValue(6,$usr->CORREO); 
        $stmt_crearcli->bindValue(7,$invoker); 
    
        $stmt_crearcli->execute();

        $result = $stmt_crearcli->fetch(PDO::FETCH_ASSOC);
        $stmt_crearcli->closeCursor(); // Necesario para ejecutar otras consultas después

        return $result ? $result['mensaje'] : 'No se recibió mensaje';
    }

    public function getPozos ():array {
        $tpozos = [];
       // $stmt_pozos  = $this->dbh->prepare("SELECT * FROM `pozo`");
        $stmt_pozos  = $this->dbh->prepare("SELECT * FROM listaclubs");
        
        // Si falla termina el programa
        $stmt_pozos->setFetchMode(PDO::FETCH_CLASS, 'Pozo_club');
    
        if ( $stmt_pozos->execute() ){
            while ( $pozo = $stmt_pozos->fetch()){
               $tpozos[]= $pozo;
            }
        }
                // Devuelvo el array de objetos
        return $tpozos;
    }

    public function getPozosFiltro ($estado):array {
        $tpozos = [];
       // $stmt_pozos  = $this->dbh->prepare("SELECT * FROM `pozo`");
        $stmt_pozos  = $this->dbh->prepare("SELECT * FROM listaclubs WHERE ID_ESTADO = $estado");
        
        // Si falla termina el programa
        $stmt_pozos->setFetchMode(PDO::FETCH_CLASS, 'Pozo_club');
    
        if ( $stmt_pozos->execute() ){
            while ( $pozo = $stmt_pozos->fetch()){
               $tpozos[]= $pozo;
            }
        }
                // Devuelvo el array de objetos
        return $tpozos;
    }


    public function getPozoRestriccion($id): array {
        $tpozos = [];
        $stmt_pozos = $this->dbh->prepare("
            SELECT 
                COUNT(*) AS total_usuarios, 
                NOW() AS HORA
            FROM 
                usuario_pozo
            WHERE 
                usuario_pozo.id_pozo = :id
        ");
    
        // Enlazar el parámetro
        $stmt_pozos->bindParam(':id', $id, PDO::PARAM_INT);
    
        // Establecer el modo de recuperación de resultados
        $stmt_pozos->setFetchMode(PDO::FETCH_ASSOC);
    
        // Ejecutar la consulta
        if ($stmt_pozos->execute()) {
            // Obtener el resultado
            $tpozos = $stmt_pozos->fetch();
        }
    
        // Devolver el array asociativo
        return $tpozos;
    }

    public function getSorteoPozo($id): array {
        $tjugadores = [];
        $stmt_tjugadores = $this->dbh->prepare("SELECT U_D.Nombre AS NOMBRED, U_R.Nombre AS NOMBRER FROM `sorteo_pozo` S INNER JOIN usuario U_D ON S.Id_JD = U_D.Id INNER JOIN usuario U_R ON S.Id_JR = U_R.Id WHERE S.Id_pozo = :id");
        
        $stmt_tjugadores->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_tjugadores->setFetchMode(PDO::FETCH_ASSOC);
    
        if ($stmt_tjugadores->execute()) {
            while ($jg = $stmt_tjugadores->fetch()) {
                $tjugadores[] = $jg;
            }
        }
    
        return $tjugadores;
    }
    
    


    public function getClubs():array{
        $tclubs=[];
        $stmt_clubs = $this->dbh->prepare("SELECT * FROM `clubs`");
        $stmt_clubs ->setFetchMode(PDO::FETCH_CLASS, 'Clubs');

        if ( $stmt_clubs->execute() ){
            while ( $club = $stmt_clubs->fetch()){
               $tclubs[]= $club;
            }
        }

        // Devuelvo el array de objetos
        return $tclubs;

    }

    public function addPozo($pozo):bool{
       
        // El id se define automáticamente por autoincremento.
        $stmt_crearpozo  = $this->dbh->prepare(
            "INSERT INTO `pozo`(`fecha_inicio_inscripcion`, `fecha_fin_inscripcion`, `precio`, `numero_jugadores_max`, `numero_jugadores_actuales`, `fecha_pozo`, `Id_club`, `ESTADO`, ID_CREADOR) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt_crearpozo->bindValue(1,$pozo->fecha_inicio_inscripcion);
        $stmt_crearpozo->bindValue(2,$pozo->fecha_fin_inscripcion);
        $stmt_crearpozo->bindValue(3,$pozo->precio);
        $stmt_crearpozo->bindValue(4,$pozo->numero_jugadores_max);
        $stmt_crearpozo->bindValue(5,$pozo->numero_jugadores_actuales);
        $stmt_crearpozo->bindValue(6,$pozo->fecha_pozo);  
        $stmt_crearpozo->bindValue(7,$pozo->Id_club);
        $stmt_crearpozo->bindValue(8,$pozo->ESTADO);
        $stmt_crearpozo->bindValue(9,$pozo->ID_CREADOR);
        $stmt_crearpozo->execute();
        $resu = ($stmt_crearpozo->rowCount () == 1);
        return $resu;
    }

    public function addClub($club):string{
       
        // El id se define automáticamente por autoincremento.
        $stmt_creaclub  = $this->dbh->prepare(
            "CALL PA_CREA_CLUB(?,?,?,?,?,?,?,?)");
        $stmt_creaclub->bindValue(1,$club->ccpp);
        $stmt_creaclub->bindValue(2,$club->Direccion);
        $stmt_creaclub->bindValue(3,$club->Latitud);
        $stmt_creaclub->bindValue(4,$club->Longitud);
        $stmt_creaclub->bindValue(5,$club->Localidad);
        $stmt_creaclub->bindValue(6,$club->Nombre);
        $stmt_creaclub->bindValue(7,$club->Observaciones);
        $stmt_creaclub->bindValue(8,$club->web);
    
        $stmt_creaclub->execute();

        $result = $stmt_creaclub->fetch(PDO::FETCH_ASSOC);
        $stmt_creaclub->closeCursor(); // Necesario para ejecutar otras consultas después

        return $result ? $result['mensaje'] : 'No se recibió mensaje';
    }


    public function getPozo ($id) {
        $pozo = false;
        $stmt_po   = $this->dbh->prepare("SELECT * FROM pozo INNER JOIN clubs ON pozo.Id_club = clubs.Id WHERE pozo.id=$id");
        $stmt_po->setFetchMode(PDO::FETCH_CLASS, 'Pozo_club');
        if ( $stmt_po->execute() ){
             if ( $obj = $stmt_po->fetch()){
                $pozo= $obj;
            }
        }
        return $pozo;
    }

    public function getClub ($id) {
        $club = false;
        $stmt_cl   = $this->dbh->prepare("select * from clubs where Id=$id");
        $stmt_cl->setFetchMode(PDO::FETCH_CLASS, 'Clubs');
        if ( $stmt_cl->execute() ){
             if ( $obj = $stmt_cl->fetch()){
                $club= $obj;
            }
        }
        return $club;
    }


    public function getJugadores ($id_pozo):array {
        try {
            // Preparar la consulta con un marcador de posición
            $stmt_jug = $this->dbh->prepare("SELECT usuario.Apodo FROM usuario INNER JOIN usuario_pozo ON usuario_pozo.id_usuario = usuario.Id WHERE usuario_pozo.id_pozo = :id_pozo");
            $stmt_pos = $this->dbh->prepare("SELECT usuario_pozo.posicion FROM usuario INNER JOIN usuario_pozo ON usuario_pozo.id_usuario = usuario.Id WHERE usuario_pozo.id_pozo = :id_pozo");
    
            // Ejecutar la consulta
            $stmt_jug->execute(array(':id_pozo' => $id_pozo));
            $stmt_pos->execute(array(':id_pozo' => $id_pozo));
    
            // Obtener los resultados y almacenarlos en el array
            $tjugadores = $stmt_jug->fetchAll(PDO::FETCH_COLUMN);
            $tposicion =$stmt_pos->fetchAll(PDO::FETCH_COLUMN);
    
            // Cerrar la sentencia
            $stmt_jug->closeCursor();
            $stmt_pos->closeCursor();

            //array con el siguiente formato: j1-p1, j2-p2...
            $tjugypos = array();
            for($i=0;$i<count($tjugadores);$i++){
                array_push($tjugypos,"$tjugadores[$i] - $tposicion[$i]");
            }

            return $tjugypos;
    

        } catch(PDOException $e) {
            // Manejar cualquier error de PDO
            throw new Exception("Error al obtener jugadores: " . $e->getMessage());
        }


    }



    public function getJugadorPozo($id, $idpozo):bool{
        $pozo = false;
        $stmt_po   = $this->dbh->prepare("SELECT * FROM `usuario_pozo` WHERE id_usuario=$id and id_pozo=$idpozo");
        $stmt_po->setFetchMode(PDO::FETCH_CLASS, 'Pozo');
        if ( $stmt_po->execute() ){
             if ( $obj = $stmt_po->fetch()){
                $pozo= true;
            }
        }
        return $pozo;
    }

    public function añadirjugadoractual($cont, $pozo){
        $stmt_addplayer  = $this->dbh->prepare("UPDATE `pozo` SET `numero_jugadores_actuales`= $cont WHERE id = $pozo");
        $stmt_addplayer->execute();
        $resu = ($stmt_addplayer->rowCount () == 1);
        return $resu;
}


    public function añadirjugpozo ($Usuario_pozo):string{
        // Crear una sentencia preparada para llamar al procedimiento almacenado
        $stmt_callProcedure = $this->dbh->prepare("CALL PA_APUNTARSE_POZO(?,?,?)");

        $stmt_callProcedure->bindValue(1,$Usuario_pozo->id_usuario);
        $stmt_callProcedure->bindValue(2,$Usuario_pozo->id_pozo); 
        $stmt_callProcedure->bindValue(3,$Usuario_pozo->posicion);

        $stmt_callProcedure->execute();
    
        // Obtener el mensaje de retorno
        $result = $stmt_callProcedure->fetch(PDO::FETCH_ASSOC);
        $stmt_callProcedure->closeCursor(); // Necesario para ejecutar otras consultas después

        // Retornar el mensaje
        return $result ? $result['mensaje'] : 'No se recibió mensaje';
    }

    public function cambiarEstado($pozo):string{
        // Crear una sentencia preparada para llamar al procedimiento almacenado
        $stmt_callProcedure = $this->dbh->prepare("CALL PA_ESTADO_POZO(?)");

        $stmt_callProcedure->bindValue(1,$pozo); 

        $stmt_callProcedure->execute();
    
        // Obtener el mensaje de retorno
        $result = $stmt_callProcedure->fetch(PDO::FETCH_ASSOC);
        $stmt_callProcedure->closeCursor(); // Necesario para ejecutar otras consultas después

        // Retornar el mensaje
        return $result ? $result['mensaje'] : 'No se recibió mensaje';
    }

    public function deleteJugPozo($id_jugador, $pozo):string{
        // Crear una sentencia preparada para llamar al procedimiento almacenado
        $stmt_callProcedure = $this->dbh->prepare("CALL BorradoPrueba2($id_jugador, $pozo)");
         // Ejecutar la sentencia preparada
        $stmt_callProcedure->execute();
        // Obtener el mensaje de retorno
        $result = $stmt_callProcedure->fetch(PDO::FETCH_ASSOC);
        $stmt_callProcedure->closeCursor(); // Necesario para ejecutar otras consultas después

        // Retornar el mensaje
        return $result ? $result['mensaje'] : 'No se recibió mensaje';
    }
    //MODIFICAR CLUB
    public function ModificarClub($club, $id_club):bool {
            $stmt_updateclub = $this->dbh->prepare(
                "UPDATE `clubs` SET `Nombre` = ?, `Localidad` = ?, `ccpp` = ?, `Direccion` = ?, `web` = ?, `Observaciones` = ?, `Latitud` = ?, `Longitud` = ? WHERE `Id` = $id_club"
            );
            $stmt_updateclub->bindValue(1,$club->Nombre);
            $stmt_updateclub->bindValue(2,$club->Localidad);
            $stmt_updateclub->bindValue(3,$club->ccpp);
            $stmt_updateclub->bindValue(4,$club->Direccion);
            $stmt_updateclub->bindValue(5,$club->web);
            $stmt_updateclub->bindValue(6,$club->Observaciones);
            $stmt_updateclub->bindValue(7,$club->Latitud);
            $stmt_updateclub->bindValue(8,$club->Longitud);
    
            $stmt_updateclub->execute();
            $resu = ($stmt_updateclub->rowCount() == 1);
            return $resu;
    }


    public function UpdatePozo($pozo): string {
        try {
            // Crear una sentencia preparada para llamar al procedimiento almacenado
            $stmt_callProcedure = $this->dbh->prepare("CALL PA_MODIFICAR_POZO(?,?,?,?,?)");
            $stmt_callProcedure->bindValue(1,$pozo->id);
            $stmt_callProcedure->bindValue(2,$pozo->precio);
            $stmt_callProcedure->bindValue(3,$pozo->fecha_fin_inscripcion);
            $stmt_callProcedure->bindValue(4,$pozo->fecha_pozo);
            $stmt_callProcedure->bindValue(5,$pozo->numero_jugadores_max);
            $stmt_callProcedure->execute();
            // Obtener el mensaje de retorno
            $result = $stmt_callProcedure->fetch(PDO::FETCH_ASSOC);
            $stmt_callProcedure->closeCursor(); // Necesario para ejecutar otras consultas después
    
            // Retornar el mensaje
            return $result ? $result['mensaje'] : 'No se recibió mensaje';
        } catch (PDOException $e) {
            // Manejo de errores
            return 'Error: ' . $e->getMessage();
        }
    }

    public function Sorteo($pozo):bool {
            // Crear una sentencia preparada para llamar al procedimiento almacenado
            $stmt_callProcedure = $this->dbh->prepare("CALL PA_INDIFERENTES($pozo)");
            $stmt_callProcedure->execute();

            $resu = ($stmt_callProcedure->rowCount() == 1);
            return $resu;

    }

    public function getUsuario(int $id_usuario): array {
        $usuario = [];
    
        // Consulta para obtener la cuenta de usuarios en posición 'D'
        $stmt_JD = $this->dbh->prepare("SELECT COUNT(*) AS cuenta_D
                                         FROM usuario_pozo UP
                                         INNER JOIN pozo P ON P.id = UP.id_pozo
                                         WHERE UP.posicion = 'D' AND P.ESTADO = 4 AND UP.id_usuario = :id_usuario");
        $stmt_JD->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_JD->execute();
        $result_JD = $stmt_JD->fetch(PDO::FETCH_ASSOC);
        $usuario['cuenta_D'] = $result_JD['cuenta_D'];
    
        // Consulta para obtener la cuenta de usuarios en posición 'R'
        $stmt_JR = $this->dbh->prepare("SELECT COUNT(*) AS cuenta_R
                                         FROM usuario_pozo UP
                                         INNER JOIN pozo P ON P.id = UP.id_pozo
                                         WHERE UP.posicion = 'R' AND P.ESTADO = 4 AND UP.id_usuario = :id_usuario");
        $stmt_JR->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_JR->execute();
        $result_JR = $stmt_JR->fetch(PDO::FETCH_ASSOC);
        $usuario['cuenta_R'] = $result_JR['cuenta_R'];
    
        // Consulta para obtener los datos del usuario
        $stmt_usuario = $this->dbh->prepare("SELECT id, Nombre, Apellidos, Apodo, CORREO
                                              FROM usuario
                                              WHERE id = :id_usuario");
        $stmt_usuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_usuario->execute();
        $result_usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
    
        // Combinar los resultados en un solo array
        $usuario = array_merge($usuario, $result_usuario);
    
        return $usuario;
    }
    
    public function NuevaContraseña($nombre, $alias, $correo) {
        $stmt_newpsw = $this->dbh->prepare("CALL PA_NUEVA_CONTRASEÑA(?,?,?)");
        $stmt_newpsw->bindValue(1, $nombre);    
        $stmt_newpsw->bindValue(2, $alias);
        $stmt_newpsw->bindValue(3, $correo);
        
        $stmt_newpsw->execute();
        
        $result = $stmt_newpsw->fetch(PDO::FETCH_ASSOC);
        $stmt_newpsw->closeCursor(); // Necesario para ejecutar otras consultas después
        
        return $result;
    }


    
    


    
}
    
