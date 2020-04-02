<?php

class especificaciones_model
{
    private $db;

    private $fichaTecnica;

    private $caracteristicas;

    private $partes;

    public function __construct()
    {
        $this->db = conectar_dbmaster::conexion();
        $this->fichaTecnica = [];
        $this->caracteristicas = [];
        $this->partes = [];
    }

    public function get_header_specifications()
    {
        $consulta = $this->db->query("CALL sp_get_header_specifications()");
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->caracteristicas[] = $filas;
        }

        return $this->caracteristicas;
    }

    public function get_data_sheet($theirPart)
    {
        $sql = "CALL sp_get_parts(?)";
        $consulta = $this->db->prepare($sql);
        $consulta->execute([$theirPart]);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->partes[] = $filas;
        }

        $contador = count($this->partes);
        $i = 1;
        $sql = "SELECT DISTINCT custoparts.cpart, specs_templateF.Header, specsF.description FROM specsF LEFT JOIN specs_templateF ON specsF.spec_Id = specs_templateF.spec_id AND specsF.field = specs_templateF.field LEFT JOIN custoparts ON specsF.part = custoparts.part WHERE (specsF.part = '".$this->partes[0]['part']."'";
        while ($i < $contador) {
            $sql .= " OR specsF.part = '".$this->partes[$i]['part']."'";
            $i++;
        }
        $sql .= ") AND LENGTH(specsF.description) > 0 ORDER BY custoparts.part, specs_templateF.Header";
        //echo $sql;exit;

        //$sql = "SELECT DISTINCT custoparts.cpart, specs_templateF.Header, specsF.description FROM specsF LEFT JOIN specs_templateF ON specsF.spec_Id = specs_templateF.spec_id AND specsF.field = specs_templateF.field LEFT JOIN custoparts ON specsF.part = custoparts.part WHERE (specsF.part = '111') AND LENGTH(specsF.description) > 0 ORDER BY custoparts.part, specs_templateF.Header";

        $this->db = conectar_dbmaster::conexion();
        $consulta = $this->db->query($sql);
        while ($filas = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $this->fichaTecnica[] = $filas;
        }

        //En caso de que el part no tenga ficha técnica
        if (empty($this->fichaTecnica)) {
            $this->fichaTecnica = null;
        }

        return $this->fichaTecnica;
    }
}