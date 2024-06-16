<?php
class Usuarios{
    private $Id;
    private $Nombre;
    private $Apellidos;
    private $Apodo;
    private $Pozos_derecha;
    private $pozos_reves;
    private $pozos_ganados;
    private $contraseña;
    private $partidos_jugados;
    private $suma_posiciones;
    private $puntos;
    private $admin;
    private $CORREO;
    

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