<?php
require_once("../db/db_datamaster.php");
require_once("../models/productos_model.php");

$make = explode("+", $_POST["make"]);
$product = new productos_model();
$product_models = $product->get_models($make[0]);
$models = "<option value='' disabled selected>Modelo</option>";

foreach ($product_models as $datos) {
    $models .= "<option value='$datos[model]+$datos[description]'>$datos[description]</option>";
}

echo $models;