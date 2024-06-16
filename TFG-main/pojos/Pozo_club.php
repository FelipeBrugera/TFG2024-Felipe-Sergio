<?php

class Pozo_club{
    private $id;
    private $fecha_inicio_inscripcion;
    private $fecha_fin_inscripcion;
    private $precio;
    private $numero_jugadores_max;
    private $numero_jugadores_actuales;
    private $fecha_pozo;
    private $Id_club;
    private $Id;
    private $Nombre;
    private $Localidad;
    private $Direccion;
    private $Link_maps;
    private $Observaciones;
    private $NOMBRE_CLUB;
    private $NOMBRE_ESTADO;
    private $ESTADO;
    private $ID_CREADOR;


    function __set($name, $value)
    {
     if ( property_exists($this,$name)){
         $this->$name = $value;
     }
    }
 
    function __get($name)
    {
        if ( property_exists($this,$name)){
            return $this->$name;
        }
    }
}