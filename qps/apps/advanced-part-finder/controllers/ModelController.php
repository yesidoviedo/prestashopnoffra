<?php
require_once("../db/DatamasterConnection.php");
require_once("../models/Datamaster.php");

$make = explode("+", $_POST["make"]);
$datamaster = new Datamaster();
$datamasterModels = $datamaster->getModels($make[0]);

echo json_encode($datamasterModels);