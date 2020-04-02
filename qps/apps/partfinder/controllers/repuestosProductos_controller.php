<?php
require_once("../db/db_datamaster.php");
require_once("../models/productos_model.php");

$model = explode("+", $_POST["model"]);
$year = $_POST["year"];
$product = new productos_model();
$product_replacements = $product->get_replacements($model[0], $year);
$replacements = "<option value='' disabled selected>Tipo de repuesto</option>";

foreach ($product_replacements as $datos) {
    $replacements .= "<option value='$datos[line]+$datos[description]'>$datos[description]</option>";
}

echo $replacements;