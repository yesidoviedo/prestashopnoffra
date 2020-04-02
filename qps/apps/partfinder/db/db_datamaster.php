<?php

class conectar_dbmaster
{
    public static function conexion()
    {
        try {
            $conexion = new PDO('mysql:host=localhost; dbname=dbmaster', 'root', 'Y_Rar&t$P9MF');
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->exec("SET CHARACTER SET UTF8");
        } catch (Exception $e) {
            die ("Error ".$e->getMessage());
            echo "Línea del error ".$e->getLine();
        }

        return $conexion;
    }
}