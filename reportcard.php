<?php
session_start();

include_once("lib/membersByRegion.lib.php");

include_once("lib/pageInterface.lib.php");
$page= new pageInterface("Statistiques");
include_once("inc/header.php");

$membersByRegion = unserialize($_SESSION["MBR"]);

?>
<div class="container theme-showcase" role="main">
		<h3><span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Statistiques</h3>

		<?php $membersByRegion->textStats(); ?>
		
		<div id="canvas">
			<div id="canvas_side" style="float:right;">

				<h4>Répartition par genre</h4>
				<div id="canvas_genre"></div>

				<h4>Répartition par décennie de naissance</h4>
				<div id="canvas_ages"></div>
			</div>

			<h4>Nombre de membres par région</h4>
			<div id="canvas_france"></div>
		</div>
</div>

<?php $membersByRegion->stats(); ?>
