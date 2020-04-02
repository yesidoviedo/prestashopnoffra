<?php

class conectar_ps_store
{
    public static function conexion()
    {
        try {
            $conexion = new PDO('mysql:host=localhost; dbname=qpselect_store', 'qpselect_master', 'masterqps2019');
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->exec("SET CHARACTER SET UTF8");
        } catch (Exception $e) {
            die ("Error ".$e->getMessage());
            echo "Línea del error ".$e->getLine();
        }

        return $conexion;
    }
}