<?php
require_once("../db/db_datamaster.php");
require_once("../models/productos_model.php");

$product = new productos_model();
$product_makes = $product->get_makes();
$makes = "<option value='' disabled selected>Marca</option>";

foreach ($product_makes as $datos) {
    $makes .= "<option value='$datos[make]+$datos[description]'>$datos[description]</option>";
}

echo $makes;