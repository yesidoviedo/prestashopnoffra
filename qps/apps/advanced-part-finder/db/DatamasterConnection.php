<?php



class DatamasterConnection

{

    public static function connection()

    {

        try {

          /*  $connection = new PDO('mysql:host=localhost; dbname=datamaster2019', 'datamast_master', 'Las maquinas me sorprenden con mucha frecuencia');*/
            $connection = new PDO('mysql:host=localhost; dbname=datamaster2019', 'root', 'Y_Rar&t$P9MF');

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $connection->exec("SET CHARACTER SET UTF8");

        } catch (Exception $e) {

            die ("Error ".$e->getMessage());

            echo "Línea del error ".$e->getLine();

        }



        return $connection;

    }

}