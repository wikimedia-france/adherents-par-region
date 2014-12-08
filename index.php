<?php
session_start();

include_once("lib/membersByRegion.lib.php");

include_once("lib/pageInterface.lib.php");
$page= new pageInterface("Liste des adhérents par région");
include_once("inc/header.php");
?>

<div class="container theme-showcase" role="main">
<div class="jumbotron">
	<h1>Liste des adhérents par région</h1>
	<p>Cet outil filtre et adapte un export des adhérents de Wikimédia France.</p>

	<div id="helpToggle" style="float: right;">[Cliquer pour afficher/cacher l'aide]</div>
</div>

<div id="helpSection" <?php if ( isset($_FILES["csv"])) { echo 'style="display: none;"';}?> >
	<h3><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Aide</h3>
	<!-- TODO : complete documentation -->
	<ol>
		<li>Sur <a href="https://dons.wikimedia.fr/" title="CiviCRM">CiviCRM</a>, faire une recherche muticritères</li>
		<li>Type de résultats : contacts — Statut de l'adhésion «&nbsp;Nouveau&nbsp;» ou «&nbsp;Current&nbsp;»</li>
		<li>Exporter les contacts</li>
		<li>Cocher «&nbsp;Sélectionner les champs à exporter&nbsp;» et choisir «&nbsp;Export mensuel adhérents&nbsp;»</li>
		<li>Cliquer sur «&nbsp;Exporter&nbsp;» et enregistrer sur l'ordinateur</li>
		<li>Importer le fichier précedemment enregistré dans le formulaire ci-desous.</li>
	</ol>
</div>

<!-- The import form -->
<h3><span class="glyphicon glyphicon-import" aria-hidden="true"></span> Import du fichier</h3>

<form role="form" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">
	<div class="form-group">
		<label for="csv">Sélectionner un fichier CSV.</label>
		<input type="file" id="csv" name="csv" value="" />
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-default">Valider</button>
	</div>
</form>

<!-- If a file has been imported, treat it. -->
<?php
if ( isset($_FILES["csv"])) {
	$csv_mimetypes = array('text/csv', 'text/x-csv', 'text/plain', 'application/csv', 'text/comma-separated-values', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext', 'application/octet-stream', 'application/txt');

		//if there was an error uploading the file
	if ($_FILES["csv"]["error"] > 0) {
		$page->alert("Return Code " . $_FILES["file"]["error"],"danger");
	} else if (!in_array($_FILES["csv"]["type"],$csv_mimetypes)) {
		$page->alert("File type error — the file doesn’t seems to be a CSV. Identified filetype: ".$_FILES["csv"]["type"].".","danger");
	} else {
		$csv = new parseCSV($_FILES["csv"]["tmp_name"]);

		$membersByRegion = new membersByRegion($csv->data);
		$membersByRegion->run();

		?>
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

		<?php  
		$membersByRegion->stats();

		$page->alert('<a href="reportcard.php">Afficher les statistiques dans une page séparée</a>');

		echo '<h3><span class="glyphicon glyphicon-export" aria-hidden="true"></span> Fichier traité</h3>';
		#check for warnings

		if(!empty($membersByRegion->warnings)) {
			foreach ($membersByRegion->warnings as $warning) {
				$page->alert($warning,"warning");
			}
		}

		if (!empty($membersByRegion->contacts)) {
		/*
		
			echo "<pre>";
			print_r($membersByRegion->counters);

		/*	echo "=====================\n";
			print_r($membersByRegion->contacts);
	/*		print_r($_FILES["csv"]); // Just in case I need to check the full csv
			print_r($csv->data); //*/ /*
			echo "</pre>";
			//*/

			$_SESSION["MBR"]=serialize($membersByRegion);
			$page->alert('<a href="export.php">Télécharger </a>',"success");
		} else {
			$page->alert('The CSV file seems to contain no data.','danger');
		}
	}
} else {
	//No file has been imported yet.
}
?>
			
</div> <!-- End of container div -->

<!--Specific Javascript for this page -->
<script>
$(document).ready(function(){
	$("#helpToggle").click(function(){
		$("#helpSection").toggle("slow");
	});
});
</script>
<?php include_once("inc/footer.php"); ?>
