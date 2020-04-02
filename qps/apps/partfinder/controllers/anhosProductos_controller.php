<?php
require_once("../db/db_datamaster.php");
require_once("../models/productos_model.php");

$model = explode("+", $_POST["model"]);
$product = new productos_model();
$product_years = $product->get_years($model[0]);
$years = "<option value='' disabled selected>Año</option>";

/*
 * Matriz usada para enviar la lista de los años de la BDD o
 * una lista de años nula junto a la lista de los repuestos
 */
$listaProductos = [];

if (count($product_years) != 0) {
    foreach ($product_years as $datos) {
        $years .= "<option value='$datos[year]'>$datos[year]</option>";
    }
} else {
    // Si no aplica el año
    $years = "<option value='' disabled selected>No aplica</option>";
    $product_replacements = $product->get_replacements_withoutYear($model[0]);
    $replacements = "<option value='' disabled selected>Tipo de repuesto</option>";

    foreach ($product_replacements as $datos) {
        $replacements .= "<option value='$datos[line]+$datos[description]'>$datos[description]</option>";
    }

    $listaProductos['replacements'] = $replacements;
}

$listaProductos['year'] = $years;
print json_encode($listaProductos);