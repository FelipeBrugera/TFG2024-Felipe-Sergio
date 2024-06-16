<?php
class Clubs{
    private $Id;
    private $Nombre;
    private $Localidad;
    private $ccpp;
    private $Direccion;
    private $web;
    private $Observaciones;
    private $Latitud;
    private $Longitud;


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