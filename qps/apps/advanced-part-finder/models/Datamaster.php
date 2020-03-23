<?php



class Datamaster

{

    private $db;



    private $records;



    public function __construct()

    {

        $this->db = DatamasterConnection::connection();

        $this->records = [];

    }



    public function getMakes()

    {

        $query = $this->db->prepare("CALL sp_get_makes()");

        $query->execute();

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function getModels($make)

    {

        $query = $this->db->prepare("CALL sp_get_models(:make)");

        $query->execute([":make" => $make]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function getYears($model)

    {

        $query = $this->db->prepare("CALL sp_get_years(:model)");

        $query->execute([":model" => $model]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function getEngines($make, $model, $year)

    {

        $query = $this->db->prepare("CALL sp_get_engines(:make, :model, :year)");

        $query->execute([":make" => $make, ":model" => $model, ":year" => $year]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function getApplicationProducts($make, $model, $year, $engine, $language, $group)

    {

        $query = $this->db->prepare("CALL sp_get_qps_products(:make, :model, :year, :engine, :language, :group)");

        $query->execute([":make" => $make, ":model" => $model, ":year" => $year, ":engine" => $engine, ":language" => $language, ":group" => $group]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function getParts($part)

    {

        $query = $this->db->prepare("CALL sp_get_parts(:part)");

        $query->execute([":part" => $part]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function getPartProducts(Array $parts, $language, $group)

    {

        $this->records = [];

        $arrayLength = count($parts);

        $i = 1;

        $sql = "SELECT DISTINCT qpselect_store.ps_product.id_product, qpselect_store.ps_product.reference, qpselect_store.ps_category_lang.name AS line, qpselect_store.ps_product_lang.name, qpselect_store.ps_product.price, qpselect_store.ps_specific_price.price AS specific_price, qpselect_store.ps_product.active FROM qpselect_store.ps_product INNER JOIN qpselect_store.ps_product_lang ON qpselect_store.ps_product.id_product = qpselect_store.ps_product_lang.id_product INNER JOIN qpselect_store.ps_category_lang ON qpselect_store.ps_product.id_category_default = qpselect_store.ps_category_lang.id_category LEFT JOIN qpselect_store.ps_specific_price ON qpselect_store.ps_product.id_product = qpselect_store.ps_specific_price.id_product AND qpselect_store.ps_specific_price.id_group = {$group} AND qpselect_store.ps_specific_price.price != -1 INNER JOIN datamaster2019.parts ON qpselect_store.ps_product.reference = datamaster2019.parts.their_part WHERE datamaster2019.parts.part IN ('".$parts[0]['part']."'";

        while ($i < $arrayLength) {

            $sql .= ", '".$parts[$i]['part']."'";

            $i++;

        }

        $sql .= ") AND qpselect_store.ps_product_lang.id_lang = {$language} AND qpselect_store.ps_category_lang.id_lang = {$language} ORDER BY qpselect_store.ps_product.active DESC";

        $query = $this->db->prepare($sql);

        $query->execute();

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }



    public function searchQPSProduct($part, $language, $group)

    {

        $this->records = [];

        $query = $this->db->prepare("CALL sp_search_qps_product(:part, :language, :group)");

        $query->execute([":part" => $part, ":language" => $language, ":group" => $group]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }

    public function searchQPSProductDescrip($part, $language, $group)

    {

        $this->records = [];

        $query = $this->db->prepare("CALL sp_search_qps_product_descrip(:part, :language, :group)");

        $query->execute([":part" => $part, ":language" => $language, ":group" => $group]);

        while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->records[] = $rows;

        }



        return $this->records;

    }

}