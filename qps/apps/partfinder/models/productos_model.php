<?php

class productos_model
{
    private $db;

    private $productos;

    public function __construct()
    {
        $this->db = conectar_dbmaster::conexion();
        $this->productos = [];
    }

    public function get_makes()
    {
        $consulta = $this->db->query("CALL sp_get_makes()");
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    public function get_models($make)
    {
        $sql = "CALL sp_get_models(:make)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":make" => $make]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    public function get_years($model)
    {
        $sql = "CALL sp_get_years(:model)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":model" => $model]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    //ESTE CASO JAMÁS OCURRIRÁ PORQUE EL CAMPO YEAR NO PUEDE SER NULL
    public function get_replacements_withoutYear($model)
    {
        $sql = "CALL sp_get_replacements_without_year(:model)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":model" => $model]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    public function get_replacements($model, $year)
    {
        $sql = "CALL sp_get_replacements(:model, :year)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":model" => $model, ":year" => $year]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    public function get_products_without_year_and_replacement($make, $model)
    {
        $sql = "CALL sp_get_products_without_year_and_replacement_qps(:make, :model)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":make" => $make, ":model" => $model]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    public function get_products_without_replacement($make, $model, $year)
    {
        $sql = "CALL sp_get_products_without_replacement_qps(:make, :model, :year)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":make" => $make, ":model" => $model, ":year" => $year]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    public function get_products($make, $model, $year, $replacement)
    {
        $sql = "CALL sp_get_products_qps(:make, :model, :year, :replacement)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":make" => $make, ":model" => $model, ":year" => $year, ":replacement" => $replacement]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }

    // CONSULTA POR CODIGO
    public function get_products_cod($codigo)
    {
        $sql = "CALL sp_get_products_cod(:codigo)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([":codigo" => $codigo]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->productos[] = $filas;
        }

        return $this->productos;
    }
}