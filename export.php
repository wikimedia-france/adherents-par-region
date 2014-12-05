<?php
session_start();

include_once("lib/membersByRegion.lib.php");

$membersByRegion	= unserialize($_SESSION["MBR"]);

$membersByRegion->export();

?>