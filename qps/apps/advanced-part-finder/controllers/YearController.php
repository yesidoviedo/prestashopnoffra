<?php
require_once("../db/DatamasterConnection.php");
require_once("../models/Datamaster.php");

$model = explode("+", $_POST["model"]);
$datamaster = new Datamaster();
$datamasterYears = $datamaster->getYears($model[0]);

echo json_encode($datamasterYears);