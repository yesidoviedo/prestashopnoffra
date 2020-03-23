<?php
require_once("../db/DatamasterConnection.php");
require_once("../models/Datamaster.php");

$datamaster = new Datamaster();
$datamasterMakes = $datamaster->getMakes();

echo json_encode($datamasterMakes);