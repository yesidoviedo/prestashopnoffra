<?php
require_once("../db/DatamasterConnection.php");
require_once("../models/Datamaster.php");

$make = explode("+", $_POST["make"]);
$model = explode("+", $_POST["model"]);
$year = $_POST["year"];
$datamaster = new Datamaster();
$datamasterEngines = $datamaster->getEngines($make[0], $model[0], $year);

echo json_encode($datamasterEngines);