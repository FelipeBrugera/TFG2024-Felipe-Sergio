<?php
class Sorteo_pozo{
    private $Id;
    private $Id_pozo;
    private $Id_JD;
    private $Id_JR;


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