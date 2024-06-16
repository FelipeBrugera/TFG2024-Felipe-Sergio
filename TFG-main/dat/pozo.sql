-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2024 a las 14:22:00
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pozo`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `BorradoPrueba2` (IN `IDJUGADOR` INT, IN `IDPOZO` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error durante la ejecución del procedimiento almacenado';
    END;

    START TRANSACTION; -- Inicia la transacción

    -- Selecciona la posición del usuario en el pozo
    -- SET POSICION = (SELECT posicion FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO);

     --  SET POSICION = 'R';

    -- Agregamos un mensaje de depuración
  --  SELECT CONCAT('POSICION:', POSICION) AS DebugMessage;



    
IF (SELECT posicion FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO) = 'R' THEN
    UPDATE pozo
    SET numero_jugadores_actuales = numero_jugadores_actuales - 1,
        Jugadores_reves = Jugadores_reves - 1
    WHERE id = IDPOZO;
    SELECT 'Has sido borrado del pozo.' AS mensaje;
    
     -- Borra las filas de usuario_pozo
    DELETE FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO;
ELSEIF (SELECT posicion FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO) = 'D' THEN
    UPDATE pozo
    SET numero_jugadores_actuales = numero_jugadores_actuales - 1,
        Jugadores_derecha = Jugadores_derecha - 1
    WHERE id = IDPOZO;
        SELECT 'Has sido borrado del pozo.' AS mensaje;
     -- Borra las filas de usuario_pozo
    DELETE FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO;
ELSEIF (SELECT posicion FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO) = 'I' THEN
    UPDATE pozo
    SET numero_jugadores_actuales = numero_jugadores_actuales - 1,
        Jugadores_indiferentes = Jugadores_indiferentes - 1
    WHERE id = IDPOZO;
        SELECT 'Has sido borrado del pozo.' AS mensaje;
     -- Borra las filas de usuario_pozo
    DELETE FROM usuario_pozo WHERE id_usuario = IDJUGADOR AND id_pozo = IDPOZO;
END IF;


   

    -- Ahora puedes usar la variable POSICION como lo necesites, por ejemplo, mostrarla en un mensaje o realizar alguna otra acción.

    COMMIT; -- Confirma la transacción
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_APUNTARSE_POZO` (IN `ID_USUARIO` INT, IN `ID_POZO` INT, IN `POSICION` CHAR(1))   BEGIN
    -- Declarar variables
    DECLARE T_USUARIOS INT DEFAULT 0; -- USUARIOS ACTUALES EN EL POZO
    DECLARE T_UPOSICION INT DEFAULT 0; -- USUARIOS ACTUALES POR POSICION
    DECLARE MAX_JUGADORES INT DEFAULT 0; -- USUARIOS MAXIMOS PERMITIDOS EN EL POZO
    DECLARE EXISTE INT DEFAULT 0; -- Variable para comprobar existencia
    

    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Comprobación si el usuario ya está apuntado al pozo
       SELECT COUNT(*) INTO EXISTE FROM usuario_pozo WHERE id_pozo = usuario_pozo.ID_POZO AND usuario_pozo.id_usuario = ID_USUARIO;
        IF EXISTE > 0 THEN
        ROLLBACK;
        SELECT 'Ya estás apuntado a este pozo.' AS mensaje;
    ELSE
        -- Asignar valores a variables locales
		SELECT COUNT(*) INTO T_USUARIOS FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO;
        SELECT COUNT(*) INTO T_UPOSICION FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO AND usuario_pozo.posicion = POSICION;
        SELECT numero_jugadores_max INTO MAX_JUGADORES FROM pozo WHERE pozo.id = ID_POZO;

        -- Intentar realizar la inserción
        IF MAX_JUGADORES / 2 > T_UPOSICION AND MAX_JUGADORES > T_USUARIOS THEN
			INSERT INTO usuario_pozo (id_usuario, id_pozo, posicion) VALUES (ID_USUARIO, ID_POZO, POSICION);
			UPDATE pozo SET pozo.numero_jugadores_actuales = pozo.numero_jugadores_actuales + 1 WHERE pozo.id = ID_POZO;
            -- Commit si no hay errores
            COMMIT;
            SELECT 'Te acabas de inscribir al pozo.' AS mensaje;
        ELSE
            -- Deshacer la transacción
            ROLLBACK;
            SELECT 'Error: El pozo ya ha alcanzado el máximo de jugadores permitidos en esta posición.' AS mensaje;
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_CREA_CLUB` (IN `p_CCPP` INT, IN `p_DIRECCION` VARCHAR(200), IN `p_LATITUD` VARCHAR(100), IN `p_LONGITUD` VARCHAR(100), IN `p_LOCALIDAD` VARCHAR(100), IN `p_NOMBRE` VARCHAR(100), IN `p_OBSERVACIONES` VARCHAR(500), IN `p_WEB` VARCHAR(200))   BEGIN
    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

    START TRANSACTION;
    
    -- Verificar si el club ya existe
    IF (SELECT COUNT(*) FROM clubs WHERE Nombre = p_NOMBRE) = 0 THEN
        INSERT INTO clubs(Nombre, Localidad, ccpp, Direccion, web, Observaciones, Latitud, Longitud)
        VALUES (p_NOMBRE, p_LOCALIDAD, p_CCPP, p_DIRECCION, p_WEB, p_OBSERVACIONES, p_LATITUD, p_LONGITUD);
        
        SELECT '1' AS mensaje;
    END IF;
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_ESTADO_POZO` (IN `ID_POZO` INT)   BEGIN
    -- Declarar variables
    DECLARE T_USUARIOS INT DEFAULT 0; -- USUARIOS ACTUALES EN EL POZO
    DECLARE J_DERECHA INT DEFAULT 0; -- USUARIOS ACTUALES POR POSICION
    DECLARE J_REVES INT DEFAULT 0; -- USUARIOS ACTUALES POR POSICION
    DECLARE MAX_JUGADORES INT DEFAULT 0; -- USUARIOS MAXIMOS PERMITIDOS EN EL POZO

    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Asignar valores a variables locales
    SELECT COUNT(*) INTO T_USUARIOS FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO;
    SELECT COUNT(*) INTO J_DERECHA FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO AND usuario_pozo.posicion = 'D';
    SELECT COUNT(*) INTO J_REVES FROM usuario_pozo WHERE id_pozo = usuario_pozo.ID_POZO AND usuario_pozo.posicion = 'R';
    SELECT numero_jugadores_max INTO MAX_JUGADORES FROM pozo WHERE pozo.id = ID_POZO;

    -- Verificar y actualizar el estado del pozo
    IF MAX_JUGADORES = T_USUARIOS THEN
        UPDATE pozo SET ESTADO = 5 WHERE id = ID_POZO;
        SELECT 'Estado modificado a 5.' AS mensaje;
    ELSEIF J_DERECHA = MAX_JUGADORES / 2 THEN
        UPDATE pozo SET ESTADO = 2 WHERE id = ID_POZO;
        SELECT 'Estado modificado a 2.' AS mensaje;
    ELSEIF J_REVES = MAX_JUGADORES / 2 THEN
        UPDATE pozo SET ESTADO = 3 WHERE id = ID_POZO;
        SELECT 'Estado modificado a 3.' AS mensaje;
    ELSE
        UPDATE pozo SET ESTADO = 1 WHERE id = ID_POZO;
        SELECT 'Estado modificado a 1.' AS mensaje;
    END IF;

    -- Commit si no hay errores
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_INDIFERENTES` (IN `ID_POZO` INT)   BEGIN

	DECLARE JDERECHA INT;
    DECLARE JREVES INT;
    DECLARE JTOTALES INT;
    DECLARE RESTANTES_R INT;
    DECLARE RESTANTES_D INT;
    DECLARE ID_JUGADOR_I INT;
    DECLARE JMITAD INT;
    DECLARE CONT_D INT DEFAULT 0;
    DECLARE CONT_R INT DEFAULT 0;
    
    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

START TRANSACTION;

	SELECT COUNT(*) INTO JDERECHA FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO AND usuario_pozo.posicion = 'D';
    SELECT COUNT(*) INTO JREVES FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO AND usuario_pozo.posicion = 'R';
    SELECT COUNT(*) INTO JTOTALES FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO;
    
    -- OPERACIONES
    SET JMITAD = JTOTALES / 2;
    SET RESTANTES_D = JMITAD - JDERECHA;
    SET RESTANTES_R = JMITAD - JREVES;
    
   -- SELECT 'OPERACIONES: ', RESTANTES_D, RESTANTES_R, JMITAD;
    
   -- MODIFICAMOS
    -- D
        WHILE RESTANTES_D > CONT_D DO
    
        
        SELECT usuario_pozo.id INTO ID_JUGADOR_I FROM usuario_pozo WHERE usuario_pozo.posicion = 'I' AND usuario_pozo.id_pozo = ID_POZO ORDER BY usuario_pozo.id_usuario ASC 
        LIMIT 1;
        
       -- SELECT RESTANTES_D, ID_JUGADOR_I;
       -- FALLA ESTO UPDATE usuario_pozo SET usuario_pozo.posicion = 'D' WHERE usuario_pozo.id = ID_JUGADOR_I;
        
        UPDATE usuario_pozo SET usuario_pozo.posicion = 'D' WHERE usuario_pozo.id = ID_JUGADOR_I;
        SET CONT_D = CONT_D + 1;
        
    END WHILE;
    	
    -- R
    WHILE RESTANTES_R > CONT_R DO
    
        SELECT usuario_pozo.id INTO ID_JUGADOR_I 
        FROM usuario_pozo 
        WHERE usuario_pozo.posicion = 'I' AND usuario_pozo.id_pozo = ID_POZO 
        ORDER BY usuario_pozo.id_usuario ASC 
        LIMIT 1;
        
        UPDATE usuario_pozo SET usuario_pozo.posicion = 'R' WHERE usuario_pozo.id = ID_JUGADOR_I;
        
        SET CONT_R = CONT_R + 1;
    END WHILE;
    
COMMIT;
CALL PA_SORTEO(ID_POZO);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_MODIFICAR_POZO` (IN `ID_POZO` INT, IN `PRECIO` DOUBLE, IN `FECHAFININSCRIPCION` DATETIME, IN `FECHAFINPOZO` DATETIME, IN `JUGMAX` INT)   BEGIN

	DECLARE JACTUALES INT;

    
    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

START TRANSACTION;

SELECT COUNT(*) INTO JACTUALES FROM usuario_pozo WHERE usuario_pozo.id_pozo = ID_POZO;

IF JACTUALES > JUGMAX THEN
	SELECT 'Error: No se puede elegir menos jugadores de los que hay apuntados.' AS mensaje;
    ROLLBACK;
ELSEIF FECHAFINPOZO < NOW() THEN
	SELECT 'Error: No se puede elegir esa fecha para el dia del pozo.' AS mensaje;
    ROLLBACK;
ELSEIF FECHAFININSCRIPCION < NOW() THEN
	SELECT 'Error: No se puede elegir esa fecha para finalizar la inscripción.' AS mensaje;
    ROLLBACK;
ELSE
	UPDATE `pozo` SET `fecha_fin_inscripcion`= FECHAFININSCRIPCION,`precio`=PRECIO,`numero_jugadores_max`=JUGMAX, `fecha_pozo`=FECHAFINPOZO WHERE POZO.id = ID_POZO;
    SELECT 'Pozo modificado con exito.' AS mensaje;
END IF;
	
    
COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_NUEVA_CONTRASEÑA` (IN `NOMBRE` VARCHAR(50), IN `ALIAS` VARCHAR(50), IN `CORREO` VARCHAR(100))   BEGIN
    DECLARE CONTADOR INT;
    DECLARE PASSWORD VARCHAR(20);
    
    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

    START TRANSACTION;
    
    -- Verificar si el usuario existe
    SELECT COUNT(*) INTO CONTADOR 
    FROM usuario 
    WHERE usuario.Nombre = NOMBRE 
      AND usuario.Apodo = ALIAS 
      AND usuario.CORREO = CORREO;
    
    IF CONTADOR > 0 THEN
        -- Generar contraseña aleatoria
        SELECT CONCAT(
            SUBSTRING(MD5(RAND()), 1, 8),  -- 8 caracteres aleatorios
            CHAR(FLOOR(RAND() * 26) + 65), -- una letra mayúscula aleatoria
            CHAR(FLOOR(RAND() * 26) + 97), -- una letra minúscula aleatoria
            FLOOR(RAND() * 10)             -- un número aleatorio del 0 al 9
        ) INTO PASSWORD;

        -- Actualizar la contraseña en la base de datos
        UPDATE usuario 
        SET usuario.contraseña = PASSWORD 
        WHERE usuario.Nombre = NOMBRE 
          AND usuario.Apodo = ALIAS 
          AND usuario.CORREO = CORREO;

        SELECT 'Revise su correo.' AS mensaje, PASSWORD AS nueva_contraseña;
    ELSE
        SELECT 'Error: Los datos introducidos no coinciden.' AS mensaje;
    END IF;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_SORTEO` (IN `ID_POZO` INT)   BEGIN

    
    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

START TRANSACTION;

-- CREACION TABLA TEMPORAL JUGADORES DERECHA

CREATE TEMPORARY TABLE TEMP_JD (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ID_JUGADOR INT
);

-- TABLA JUGADORES REVES

CREATE TEMPORARY TABLE TEMP_JR (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ID_JUGADOR INT
);

-- INSERTAMOS LOS JUGADORES POR POSICION EN CADA TABLA

INSERT INTO TEMP_JD(ID_JUGADOR)
SELECT usuario_pozo.id_usuario FROM usuario_pozo WHERE usuario_pozo.posicion = 'D' AND usuario_pozo.id_pozo = ID_POZO ORDER BY RAND();

INSERT INTO TEMP_JR(ID_JUGADOR)
SELECT usuario_pozo.id_usuario FROM usuario_pozo WHERE usuario_pozo.posicion = 'R' AND usuario_pozo.id_pozo = ID_POZO ORDER BY RAND();

-- UNIMOS LAS 2 TABLAS EN 1
	
INSERT INTO sorteo_pozo(Id_JD, Id_JR, Id_pozo)
SELECT D.ID_JUGADOR, R.ID_JUGADOR, ID_POZO
FROM TEMP_JD D
JOIN TEMP_JR R ON D.id = R.id;

UPDATE pozo set pozo.ESTADO = 6 WHERE pozo.id = ID_POZO;
    -- Eliminar tablas temporales
    DROP TEMPORARY TABLE IF EXISTS TEMP_JD;
    DROP TEMPORARY TABLE IF EXISTS TEMP_JR;
    
COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PA_USUARIO` (IN `ID` INT, IN `NOMBRE` VARCHAR(50), IN `APELLIDOS` VARCHAR(50), IN `APODO` VARCHAR(50), IN `CONTRASEÑA` VARCHAR(50), IN `CORREO` VARCHAR(100), IN `INVOKER` INT)   BEGIN
    DECLARE CALIAS INT;
    DECLARE CMAIL INT;


    -- Declarar manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Manejo de errores
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema.' AS mensaje;
    END;

    START TRANSACTION;

    IF INVOKER = 1 THEN
        -- Inserción de nuevo usuario si no existe el alias
        SELECT COUNT(*) INTO CALIAS FROM usuario WHERE usuario.Apodo = APODO;
        SELECT COUNT(*) INTO CMAIL FROM usuario WHERE usuario.CORREO = CORREO;
        
        IF CALIAS = 0 AND CMAIL = 0 THEN
            INSERT INTO usuario (`Nombre`, `Apellidos`, `Apodo`, `contraseña`, `CORREO`)
            VALUES (NOMBRE, APELLIDOS, APODO, CONTRASEÑA, CORREO);
            SELECT 'Usuario creado con exito.' AS mensaje;
        ELSE
        	SELECT 'El apodo o correo ya existen.' AS mensaje;
        END IF;
    ELSEIF INVOKER = 2 THEN
        -- Actualización de usuario si el alias no se repite (excepto para el propio usuario)
        SELECT COUNT(*) INTO CALIAS FROM usuario WHERE usuario.Apodo = APODO AND usuario.Id <> ID;
        SELECT COUNT(*) INTO CMAIL FROM usuario WHERE usuario.CORREO = CORREO AND usuario.Id <> ID;
        
        IF CALIAS = 0 AND CMAIL = 0 THEN
            IF CONTRASEÑA != '' THEN
                UPDATE `usuario`
                SET `Nombre` = NOMBRE,
                    `Apellidos` = APELLIDOS,
                    `Apodo` = APODO,
                    `contraseña` = CONTRASEÑA,
                    `CORREO` = CORREO
                WHERE usuario.Id = ID;
                SELECT 'Usuario modificado con exito.' AS mensaje;
            ELSE
                UPDATE `usuario`
                SET `Nombre` = NOMBRE,
                    `Apellidos` = APELLIDOS,
                    `Apodo` = APODO,
                    `CORREO` = CORREO
                WHERE usuario.Id = ID;
                SELECT 'Usuario modificado con exito.' AS mensaje;
            END IF;
        ELSE
        	SELECT 'El apodo o correo ya existen.' AS mensaje;
        END IF;
    END IF;

    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubs`
--

CREATE TABLE `clubs` (
  `Id` int(11) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Localidad` varchar(50) DEFAULT NULL,
  `ccpp` int(5) NOT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `web` varchar(500) NOT NULL,
  `Observaciones` varchar(300) NOT NULL,
  `Latitud` varchar(100) NOT NULL,
  `Longitud` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clubs`
--

INSERT INTO `clubs` (`Id`, `Nombre`, `Localidad`, `ccpp`, `Direccion`, `web`, `Observaciones`, `Latitud`, `Longitud`) VALUES
(1, 'Padel Indoor BMW', 'Alcala de henares', 28806, 'C. Argentina, 11, 28806 Alcalá de Henares, Madrid', '', 'Al lado del concesionario', '40.49798025463114', '-3.39652419090271'),
(5, 'G2 Padel', 'daganzo', 0, 'isaac peral', '', 'esta en un poligono', '40.53852538278962', '-3.457517623901367'),
(6, 'willy', 'Alcala de henares', 28806, 'Leonardo torres quevedo', 'http://www.clubdeportivowillygarena.com/', '', '', ''),
(8, 'Punta Chica', 'sdoihfsd', 0, 'dubsf`dh', 'oìsd+oihsd', 'oiu`dshf+', '', ''),
(9, 'Wanda', 'Alcala de henares', 28806, 'Espartales', '', 'campo del atleti', '40.50825244269745', '-3.3609580993652344'),
(10, 'Cisneros', 'Alcala de henares', 28806, 'Cuesta Teatinos', '', '', '40.4969690331583', '-3.3606013655662537'),
(11, 'Green Village', 'Meco', 28880, 'no la se', '', '', '40.55094214026262', '-3.3356809616088867'),
(15, 'GALEPADEL', 'Alcalá de henares', 28806, 'dsifisdu', '', '', '40.491544278636155', '-3.4036124821504092'),
(21, 'werew', 'ewrwe', 32432, '', '', '', '', ''),
(22, 'culb tetuan', 'Madrid', 28080, 'diosjfsdoi', '', '', '40.432052961738364', '-3.686045959444738');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `ID_ESTADO` int(11) NOT NULL,
  `NOMBRE` varchar(100) NOT NULL,
  `DESCRIPCION` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`ID_ESTADO`, `NOMBRE`, `DESCRIPCION`) VALUES
(1, 'POZO DISPONIBLE', 'Quedan plazas disponibles de cualquier posición'),
(2, 'SOLO REVES', 'Todas las plazas de derecha estan llenas'),
(3, 'SOLO DERECHA', 'Todas las plazas de reves estan llenas'),
(4, 'TERMINADO', 'Este pozo ya se ha finalizado.'),
(5, 'POZO LLENO', 'Todas las plazas estan asignadas'),
(6, 'SORTEO REALIZADO', '');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `listaclubs`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `listaclubs` (
`NOMBRE_CLUB` varchar(50)
,`Localidad` varchar(50)
,`fecha_pozo` datetime
,`precio` double
,`numero_jugadores_max` int(11)
,`numero_jugadores_actuales` int(11)
,`NOMBRE_ESTADO` varchar(100)
,`ID_ESTADO` int(11)
,`id` int(11)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pozo`
--

CREATE TABLE `pozo` (
  `id` int(11) NOT NULL,
  `fecha_inicio_inscripcion` date DEFAULT NULL,
  `fecha_fin_inscripcion` datetime DEFAULT NULL,
  `precio` double NOT NULL,
  `numero_jugadores_max` int(11) DEFAULT NULL,
  `numero_jugadores_actuales` int(11) DEFAULT NULL,
  `Jugadores_reves` int(11) NOT NULL,
  `Jugadores_derecha` int(11) NOT NULL,
  `Jugadores_indiferentes` int(11) NOT NULL,
  `fecha_pozo` datetime NOT NULL,
  `Id_club` int(11) DEFAULT NULL,
  `ESTADO` int(11) NOT NULL,
  `ID_CREADOR` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pozo`
--

INSERT INTO `pozo` (`id`, `fecha_inicio_inscripcion`, `fecha_fin_inscripcion`, `precio`, `numero_jugadores_max`, `numero_jugadores_actuales`, `Jugadores_reves`, `Jugadores_derecha`, `Jugadores_indiferentes`, `fecha_pozo`, `Id_club`, `ESTADO`, `ID_CREADOR`) VALUES
(11, '2024-03-07', '2024-03-22 00:00:00', 20, 840, 5, 2, 2, 0, '2024-03-13 00:00:00', 1, 0, 0),
(12, '2024-03-07', '2024-03-08 00:00:00', 10, 24, 6, 1, 3, 0, '2024-03-10 00:00:00', 5, 1, 0),
(13, '2024-03-28', '2024-03-29 00:00:00', 20, 24, 2, 0, 2, 0, '2024-03-31 00:00:00', 8, 0, 0),
(14, '2024-04-07', '2024-04-12 00:00:00', 10, 24, 5, 3, 1, 1, '2024-04-14 00:00:00', 10, 0, 0),
(15, '2024-04-07', '2024-04-19 00:00:00', 10, 16, 13, 1, 11, 1, '2024-04-21 00:00:00', 11, 2, 0),
(16, '2024-05-23', '2024-05-23 00:00:00', 10, 4, 6, -1, -2, 0, '0000-00-00 00:00:00', 1, 1, 0),
(23, '2024-05-30', '2024-06-07 14:02:00', 9, 8, 1, -1, 0, -2, '2024-06-09 12:15:00', 1, 5, 0),
(24, '2024-06-03', '2024-06-03 19:08:00', 10, 8, 0, 0, 0, 0, '2024-06-03 21:10:00', 1, 6, 0),
(25, '2024-06-04', '2024-06-04 01:04:00', 20, 8, 0, 0, 0, 0, '2024-06-04 01:05:00', 1, 6, 0),
(27, '2024-06-13', '2024-06-14 10:30:00', 10, 16, 0, 0, 0, 0, '2024-06-16 10:30:00', 5, 1, 0),
(28, '2024-06-13', '2024-06-14 10:30:00', 10, 16, 0, 0, 0, 0, '2024-06-16 10:30:00', 5, 1, 1),
(29, '2024-06-13', '2024-06-13 10:48:00', 20, 8, 0, 0, 0, 0, '2024-06-16 10:48:00', 15, 1, 1),
(33, '2024-06-14', '0000-00-00 00:00:00', 10, 8, 0, -1, 0, 0, '2024-06-14 00:38:00', 5, 1, 1),
(34, '2024-06-14', '2024-06-15 12:45:00', 10, 8, 0, 0, -1, 0, '2024-06-16 12:45:00', 22, 1, 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sorteo_pozo`
--

CREATE TABLE `sorteo_pozo` (
  `Id` int(11) NOT NULL,
  `Id_pozo` int(11) NOT NULL,
  `Id_JD` int(11) NOT NULL,
  `Id_JR` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sorteo_pozo`
--

INSERT INTO `sorteo_pozo` (`Id`, `Id_pozo`, `Id_JD`, `Id_JR`) VALUES
(71, 25, 6, 5),
(72, 25, 1, 8),
(73, 25, 3, 9),
(74, 25, 4, 7),
(78, 24, 5, 6),
(79, 24, 7, 9),
(80, 24, 3, 8),
(81, 24, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `Id` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellidos` varchar(50) NOT NULL,
  `Apodo` varchar(50) NOT NULL,
  `Pozos_derecha` int(11) DEFAULT NULL,
  `pozos_reves` int(11) DEFAULT NULL,
  `pozos_ganados` int(11) DEFAULT NULL,
  `contraseña` varchar(50) DEFAULT NULL,
  `partidos_jugados` int(11) DEFAULT NULL,
  `suma_posicion` int(11) DEFAULT NULL,
  `puntos` int(11) DEFAULT NULL,
  `admin` int(11) DEFAULT NULL,
  `CORREO` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`Id`, `Nombre`, `Apellidos`, `Apodo`, `Pozos_derecha`, `pozos_reves`, `pozos_ganados`, `contraseña`, `partidos_jugados`, `suma_posicion`, `puntos`, `admin`, `CORREO`) VALUES
(1, 'sergio', 'marco', 'sergi', NULL, NULL, NULL, '123', NULL, NULL, NULL, 1, 'sergiomarcoblazquez@hotmail.com'),
(3, 'pepe', 'lopez', 'pepito', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(4, 'luis', 'strack', 'luisito', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(5, 'luis', 'strack', 'luisito', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(6, 'king', 'dani', 'kingkimg', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(7, 'kule', 'kuleador', 'kulo', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(8, 'felipe', 'isdnfgoisnf', 'felipe', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(9, 'Alvaro', 'Hernandez Sanz', 'Pancets', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(10, 'Pedro', 'Dominguez', 'akatuputopadre', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(11, 'Alejandro', 'Abellan', 'Ale', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(12, 'Carol', 'Blazquez', 'carolb', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(13, 'Antonio', 'Marco', 'am', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(14, 'mar', 'dsnfios', 'mar', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(15, 'prueb', 'prueba', 'pr', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(16, 'prueb', 'prueba', 'pr', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, ''),
(17, 'Irene', 'Marco Blázquez', 'Airin ', NULL, NULL, NULL, 'Irene2024', NULL, NULL, NULL, NULL, ''),
(19, 'Jose', 'asdsf', 'josete', NULL, NULL, NULL, '$2y$10$2QJ9zmfglo5dlddiIK2Oh.mg8VD0DvdbQvVkxUQcgRU', NULL, NULL, NULL, NULL, 'fsfsd'),
(20, 'dsfds', 'sdfsdf', 'antonio', NULL, NULL, NULL, '$2y$10$rD4iz8mwsqRJUdritbFUPOVRNFPIlRIQHwACKJ8n4RK', NULL, NULL, NULL, NULL, 'sdfsd'),
(21, 'dfgdf', 'dfgdf', 'sergito', NULL, NULL, NULL, '$2y$10$x6cQcmOMbQGZ6.rPzoQKdurD9XviQm6srZt7miAwl5K', NULL, NULL, NULL, NULL, 'sdfds'),
(22, 'pepe', 'lopez', 'pepin', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, 'madridalcala2012@gmail.com'),
(23, 'Carol', 'B', 'Caroline', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, 'carol.blazque@gmail.com'),
(24, 'Felipe', 'Brugera', 'Felipin', NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, 'fmbp011202@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_pozo`
--

CREATE TABLE `usuario_pozo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `posicion` char(1) DEFAULT NULL,
  `id_pozo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_pozo`
--

INSERT INTO `usuario_pozo` (`id`, `id_usuario`, `posicion`, `id_pozo`) VALUES
(62, 1, 'R', 15),
(76, 4, 'D', 16),
(80, 6, 'R', 16),
(81, 1, 'R', 11),
(85, 3, 'D', 23),
(86, 4, 'D', 23),
(87, 5, 'D', 23),
(88, 6, 'R', 23),
(89, 7, 'R', 23),
(90, 8, 'R', 23),
(91, 9, 'R', 23),
(94, 1, 'D', 23),
(95, 1, 'R', 24),
(96, 3, 'D', 24),
(97, 4, 'D', 24),
(98, 5, 'D', 24),
(99, 6, 'R', 24),
(100, 7, 'D', 24),
(101, 8, 'R', 24),
(102, 9, 'R', 24),
(103, 1, 'D', 25),
(104, 3, 'D', 25),
(105, 4, 'D', 25),
(106, 5, 'R', 25),
(107, 6, 'D', 25),
(108, 7, 'R', 25),
(109, 8, 'R', 25),
(110, 9, 'R', 25),
(111, 23, 'D', 12),
(114, 24, 'R', 12);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_usuario_pozo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_usuario_pozo` (
`total_usuarios` bigint(21)
,`id_pozo` int(11)
,`HORA` datetime
);

-- --------------------------------------------------------

--
-- Estructura para la vista `listaclubs`
--
DROP TABLE IF EXISTS `listaclubs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listaclubs`  AS SELECT `c`.`Nombre` AS `NOMBRE_CLUB`, `c`.`Localidad` AS `Localidad`, `p`.`fecha_pozo` AS `fecha_pozo`, `p`.`precio` AS `precio`, `p`.`numero_jugadores_max` AS `numero_jugadores_max`, `p`.`numero_jugadores_actuales` AS `numero_jugadores_actuales`, `e`.`NOMBRE` AS `NOMBRE_ESTADO`, `e`.`ID_ESTADO` AS `ID_ESTADO`, `p`.`id` AS `id` FROM ((`pozo` `p` left join `estados` `e` on(`p`.`ESTADO` = `e`.`ID_ESTADO`)) left join `clubs` `c` on(`p`.`Id_club` = `c`.`Id`)) GROUP BY `p`.`fecha_pozo` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_usuario_pozo`
--
DROP TABLE IF EXISTS `vista_usuario_pozo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_usuario_pozo`  AS SELECT count(0) AS `total_usuarios`, `usuario_pozo`.`id_pozo` AS `id_pozo`, current_timestamp() AS `HORA` FROM `usuario_pozo` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`Id`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`ID_ESTADO`);

--
-- Indices de la tabla `pozo`
--
ALTER TABLE `pozo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_referenciada` (`Id_club`);

--
-- Indices de la tabla `sorteo_pozo`
--
ALTER TABLE `sorteo_pozo`
  ADD PRIMARY KEY (`Id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`Id`);

--
-- Indices de la tabla `usuario_pozo`
--
ALTER TABLE `usuario_pozo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_pozo` (`id_pozo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clubs`
--
ALTER TABLE `clubs`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `ID_ESTADO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pozo`
--
ALTER TABLE `pozo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `sorteo_pozo`
--
ALTER TABLE `sorteo_pozo`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuario_pozo`
--
ALTER TABLE `usuario_pozo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pozo`
--
ALTER TABLE `pozo`
  ADD CONSTRAINT `fk_referenciada` FOREIGN KEY (`Id_club`) REFERENCES `clubs` (`Id`);

--
-- Filtros para la tabla `usuario_pozo`
--
ALTER TABLE `usuario_pozo`
  ADD CONSTRAINT `usuario_pozo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`Id`),
  ADD CONSTRAINT `usuario_pozo_ibfk_2` FOREIGN KEY (`id_pozo`) REFERENCES `pozo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
