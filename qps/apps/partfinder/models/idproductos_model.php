<?php

class idproductos_model
{
    private $db;

    private $idproductos;

    public function __construct()
    {
        $this->db = conectar_ps_store::conexion();
        $this->idproductos = [];
    }

    public function get_id_products($reference)
    {
        $sql = "CALL sp_get_id_products(:reference)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":reference" => $reference]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->idproductos[] = $filas;
        }

        return $this->idproductos;
    }
}