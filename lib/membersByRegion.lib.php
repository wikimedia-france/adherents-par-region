<?php

include_once("parsecsv.lib.php");
include_once("france-subdivisions.lib.php");

class membersByRegion {
	///
	/// Properties
	///

	/**
	 * Contacts rendered as an array
	 * @var array
	 */
	public $contacts;

	/**
	 * Warnings issued by the run() function
	 * @var array
	 */
	public $warnings;	

	/**
	 * CSV data
	 * @var array
	 */
	private $csv_data;

	/**
	 * Counters for some statistics
	 * @var array;
	 */
	 public $counters;

	///
	/// Functions
	///

	/**
	 * Initializes a new instance of the csv2QuickStatements class
	 *
	 * @param array $csv_data the data from the CSV
	 */
	public function __construct ($csv_data) {
		$this->csv_data	= $csv_data;
		$this->contacts	= array();
		$this->warnings = array();

		$this->counters = array();
		$this->counters["total"] = count($this->csv_data);
		$this->counters["gender"]["male"] = 0 ;
		$this->counters["gender"]["female"] = 0 ;

		$this->counters["departements"] = array();
		$this->counters["regions"] = array();
		$this->counters["regions"]["FR97"] = 0 ;
		$this->counters["regions"]["FR98"] = 0 ;
		$this->counters["regions"]["FR99"] = 0 ;
		$this->counters["regions"]["FRA1"] = 0 ;
		$this->counters["regions"]["FRA2"] = 0 ;
		$this->counters["regions"]["FRA3"] = 0 ;
		$this->counters["regions"]["FRA4"] = 0 ;
		$this->counters["regions"]["FRA5"] = 0 ;
		$this->counters["regions"]["FRA6"] = 0 ;
		$this->counters["regions"]["FRA7"] = 0 ;
		$this->counters["regions"]["FRA8"] = 0 ;
		$this->counters["regions"]["FRA9"] = 0 ;
		$this->counters["regions"]["FRB1"] = 0 ;
		$this->counters["regions"]["FRB2"] = 0 ;
		$this->counters["regions"]["FRB3"] = 0 ;
		$this->counters["regions"]["FRB4"] = 0 ;
		$this->counters["regions"]["FRB5"] = 0 ;
		$this->counters["regions"]["FRB6"] = 0 ;
		$this->counters["regions"]["FRB7"] = 0 ;
		$this->counters["regions"]["FRB8"] = 0 ;
		$this->counters["regions"]["FRB9"] = 0 ;
		$this->counters["regions"]["FRC1"] = 0 ;

		$this->counters["birthdate"] = array();
		$this->counters["birthdecade"] = array();
	}

	public function run() {
		foreach ($this->csv_data as $entry) {
			$contact= array();

			foreach ($entry as $key => $value) {
				
				switch ($key) {
					case '':
						$this->warnings[] = 'Unidentified property for value '.$value.'.';
						break;
					case 'Nom affiché': 
						$contact["name"] = $value;
						break;
					case 'Surnom':
						$contact["nickname"] = $value;
						break;
					case 'Date de naissance': 
						$contact["birthdate"] = $value;
						if (!empty($value)) {
							$year = substr($value, 0, 4);

							if (!empty($this->counters["birthdate"][$year])) {
								$this->counters["birthdate"][$year]++;
							} else {
								$this->counters["birthdate"][$year] = 1;
							}

							$decade = round($year, -1)."s";
							if (!empty($this->counters["birthdecade"][$decade])) {
								$this->counters["birthdecade"][$decade]++;
							} else {
								$this->counters["birthdecade"][$decade] = 1;
							}
						}
						break;
					case 'Sexe':
						if ($value == 'Homme') { $this->counters["gender"]["male"]++; }
						else if ($value == 'Femme') { $this->counters["gender"]["female"]++; }
						$contact["gender"] = $value;
						break;
					case 'Domicile-Courriel':
						$contact["courriel"] = $value;
						break;
					case 'Domicile-Téléphone-Téléphone':
						$contact["phone"] = $value;
						break;
					case 'Domicile-Rue':
						$contact["street"] = $value;
						break;
					case "Domicile-Complément d'adresse 1":
						$contact["address1"] = $value;
						break;
					case "Domicile-Complément d'adresse 2":
						$contact["address2"] = $value;
						break;
					case "Domicile-Code postal":
						$contact["postcode"] = $value;
						break;
					case "Domicile-Ville":
						$contact["city"] = $value;
						break;
					case "Domicile-Pays":
						$contact["country"] = $value;
						break;
					default:
						$this->warnings[] = 'Unknown property '.$key.'.';
						break;
				}
			}

			if ($contact["country"] == 'France') {
				$departement = substr($contact["postcode"], 0, 2);
				$contact["departement"] = $departement;

				if (isset($this->counters["departements"][$departement])) {
					$this->counters["departements"][$departement]++;
				} else {
					$this->counters["departements"][$departement]=1;
				}

				$region = franceSubdivisions::$dep2region[$departement];
				$contact["region"] = $region;

				if (isset($this->counters["regions"][$region])) {
					$this->counters["regions"][$region]++;
				} else {
					$this->counters["regions"][$region]=1;
				}

				if ($contact["departement"] != "97") { $this->contacts[] = $contact; } // For now we only want contacts living in Metropolitan France
			} else {
				$contact["departement"] = "";
				$contact["region"] = "";
			}

			//$this->contacts[] = $contact; For now we only want contacts living in France
		}

		ksort($this->counters["departements"]);
		ksort($this->counters["regions"]);
		ksort($this->counters["birthdate"]);
		ksort($this->counters["birthdecade"]);

		function cmp($a, $b) {
    		return strcmp($a["region"], $b["region"]);
		}

		usort($this->contacts, "cmp");
	}

	public function textStats(){
		setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
		$date = strftime("%e %B %Y");
		$total = $this->counters["total"];
		$html = "<p>Au $date, l’association compte $total adhérents, dont :</p>";
		$html.= "<ul>";
		$html.= "<li>".array_sum($this->counters["gender"]).' ont indiqué leur genre.</li>';
		$html.= '<li>'.array_sum($this->counters["birthdecade"]).' ont indiqué leur date de naissance.</li>';
		$html.= "</ul>";

		echo $html;
	}

	public function stats() {
  		$html= '<script src="js/france_regions.js" charset="utf-8" ></script>'."\n";

  		if (!empty($this->counters["regions"])) {
  			$html.= '<script type="text/javascript">'."\n";

  			foreach ($this->counters["regions"] as $key => $value) {
  				if ($key != "DOM-TOM") {
  					switch ($value) {
	  					case 0:
	  						$color = "#fff";
	  						break;
	  					case ($value >= 1 && $value <=5):
	  						$color = "#d7e3f4";
	  						break;
	  					case ($value > 5 && $value <= 10):
	  						$color = "#87aade";
	  						break;
	  					case ($value > 10 && $value <= 25):
	  						$color = "#3771c8";
							break;
						case ($value > 25 && $value <= 50):
							$color = "#214478";
							break;
						case ($value > 50 && $value <= 100):
							$color = "#162d50";
							break;
						case ($value > 100):
							$color = "#0b1728";
							break;
	  				}

	  				if ($value > 1) { $plural="s";} else {$plural="";}
	  				$title = franceSubdivisions::$region_name[$key]." : ".$value." membre$plural.";

  				$html.= 'region.'.$key.'.attr({fill: "'.$color.'", title: "'.$title.'"});'."\n";
  				}
  			}
	  		$html.= '</script>';
  		}  		

		if (!empty($this->counters["gender"])) {
			$html.= '<script type="text/javascript">'."\n";
			$html.= "var genderCanvas = Raphael(document.getElementById('canvas_genre'), 400, 250);\n";
			$male = $this->counters["gender"]["male"];
			$female = $this->counters["gender"]["female"];


			$html.= "gender = genderCanvas.piechart(120, 120, 100, [$male, $female], { legend: ['$male hommes (%%.%%)', '$female femmes (%%.%%)'], legendpos: 'east', colors: ['#038','#59f']});\n";

			$html.= "gender.hover(function () {
						this.sector.stop();
						this.sector.scale(1.1, 1.1, this.cx, this.cy);
						if (this.label) {
							this.label[0].stop();
							this.label[0].attr({ r: 7.5 });
							this.label[1].attr({ 'font-weight': 800 });
						}
						}, function () {
							this.sector.animate({ transform: 's1 1 ' + this.cx + ' ' + this.cy }, 500, 'bounce');
							if (this.label) {
							this.label[0].animate({ r: 5 }, 500, 'bounce');
							this.label[1].attr({ 'font-weight': 400 });
							}
						});\n";

			$html.= '</script>';

		}

		if (!empty($this->counters["birthdecade"])) {
			$html.= '<script type="text/javascript">'."\n";
			$html.= "var birthdateCanvas = new Raphael(document.getElementById('canvas_ages'), 400, 300);\n";
			$html.= "var fin = function () {
						this.flag = birthdateCanvas.popup(this.bar.x, this.bar.y, this.bar.value || '0').insertBefore(this);
					},
					fout = function () {
						this.flag.animate({opacity: 0}, 300, function () {this.remove();});
					};\n";


			$bars= implode(",", $this->counters["birthdecade"]);
			$labels = implode(",", array_keys($this->counters["birthdecade"]));

			$y=25;
			$hop = 245/(count($this->counters["birthdecade"]));
			foreach ($this->counters["birthdecade"] as $key => $value) {
				$html.= "birthdateCanvas.text(10,$y, '$key : $value').attr({ font: '14px sans-serif', 'text-anchor': 'start' });\n";
				$y+=$hop;
			}
			$html.= "var birthdates = birthdateCanvas.hbarchart(100, 10, 250, 250, [[$bars]]).hover(fin, fout);\n";
			$html.= '</script>';
		}

		echo $html;
	}



	public function export() {
		$csv = new parseCSV();

		$fields = array("Nom","Surnom","Date de naissance","Genre","Courriel","Téléphone","Rue", "Complément adresse 1", "Complément adresse 2", "Code postal", "Ville", "Pays", "Département", "Région");

		$csv->output('membersByRegion.csv', $this->contacts, $fields, ',');
	}
	

}