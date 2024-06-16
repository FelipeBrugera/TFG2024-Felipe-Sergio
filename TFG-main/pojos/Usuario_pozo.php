<?php
class Usuario_pozo{
    private $id;
    private $id_usuario;
    private $posicion;
    private $id_pozo;


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