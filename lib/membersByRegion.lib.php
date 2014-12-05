<?php

include_once("parsecsv.lib.php");

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

	 /**
	  * Map the departements to the corresponding region
	  * @var array;
	  */
	  private $dep2region = array (
	  		"01"	=> "Rhône-Alpes",
	  		"02"	=> "Picardie",
	  		"03"	=> "Auvergne",
	  		"04"	=> "Provence-Alpes-Côte d'Azur",
	  		"05"	=> "Provence-Alpes-Côte d'Azur",
	  		"06"	=> "Provence-Alpes-Côte d'Azur",
	  		"07"	=> "Rhône-Alpes",
	  		"08"	=> "Champagne-Ardenne",
	  		"09"	=> "Midi-Pyrénées",
	  		"10"	=> "Champagne-Ardenne",
	  		"11"	=> "Languedoc-Roussillon",
	  		"12"	=> "Midi-Pyrénées",
	  		"13"	=> "Provence-Alpes-Côte d'Azur",
	  		"14"	=> "Basse-Normandie",
	  		"15"	=> "Auvergne",
	  		"16"	=> "Poitou-Charentes",
	  		"17"	=> "Poitou-Charentes",
	  		"18"	=> "Centre",
	  		"19"	=> "Limousin",
	  		"2A"	=> "Corse",
	  		"2B"	=> "Corse",
	  		"20"	=> "Corse",
	  		"21"	=> "Bourgogne",
	  		"22"	=> "Bretagne",
	  		"23"	=> "Limousin",
	  		"24"	=> "Aquitaine",
	  		"25"	=> "Franche-Comté",
	  		"26"	=> "Rhône-Alpes",
	  		"27"	=> "Haute-Normandie",
	  		"28"	=> "Centre",
	  		"29"	=> "Bretagne",
	  		"30"	=> "Languedoc-Roussillon",
	  		"31"	=> "Midi-Pyrénées",
	  		"32"	=> "Midi-Pyrénées",
	  		"33"	=> "Aquitaine",
	  		"34"	=> "Languedoc-Roussillon",
	  		"35"	=> "Bretagne",
	  		"36"	=> "Centre",
	  		"37"	=> "Centre",
	  		"38"	=> "Rhône-Alpes",
	  		"39"	=> "Franche-Comté",
	  		"40"	=> "Aquitaine",
	  		"41"	=> "Centre",
	  		"42"	=> "Rhône-Alpes",
	  		"43"	=> "Auvergne",
	  		"44"	=> "Pays de la Loire",
	  		"45"	=> "Centre",
	  		"46"	=> "Midi-Pyrénées",
	  		"47"	=> "Aquitaine",
	  		"48"	=> "Languedoc-Roussillon",
	  		"49"	=> "Pays de la Loire",
	  		"50"	=> "Basse-Normandie",
	  		"51"	=> "Champagne-Ardenne",
	  		"52"	=> "Champagne-Ardenne",
	  		"53"	=> "Pays de la Loire",
	  		"54"	=> "Lorraine",
	  		"55"	=> "Lorraine",
	  		"56"	=> "Bretagne",
	  		"57"	=> "Lorraine",
	  		"58"	=> "Bourgogne",
	  		"59"	=> "Nord-Pas-de-Calais",
	  		"60"	=> "Picardie",
	  		"61"	=> "Basse-Normandie",
	  		"62"	=> "Nord-Pas-de-Calais",
	  		"63"	=> "Auvergne",
	  		"64"	=> "Aquitaine",
	  		"65"	=> "Midi-Pyrénées",
	  		"66"	=> "Languedoc-Roussillon",
	  		"67"	=> "Alsace",
	  		"68"	=> "Alsace",
	  		"69"	=> "Rhône-Alpes",
	  		"70"	=> "Franche-Comté",
	  		"71"	=> "Bourgogne",
	  		"72"	=> "Pays de la Loire",
	  		"73"	=> "Rhône-Alpes",
	  		"74"	=> "Rhône-Alpes",
	  		"75"	=> "Île-de-France",
	  		"76"	=> "Haute-Normandie",
	  		"77"	=> "Île-de-France",
	  		"78"	=> "Île-de-France",
	  		"79"	=> "Poitou-Charentes",
	  		"80"	=> "Picardie",
	  		"81"	=> "Midi-Pyrénées",
	  		"82"	=> "Midi-Pyrénées",
	  		"83"	=> "Provence-Alpes-Côte d'Azur",
	  		"84"	=> "Provence-Alpes-Côte d'Azur",
	  		"85"	=> "Pays de la Loire",
	  		"86"	=> "Poitou-Charentes",
	  		"87"	=> "Limousin",
	  		"88"	=> "Lorraine",
	  		"89"	=> "Bourgogne",
	  		"90"	=> "Franche-Comté",
	  		"91"	=> "Île-de-France",
	  		"92"	=> "Île-de-France",
	  		"93"	=> "Île-de-France",
	  		"94"	=> "Île-de-France",
	  		"95"	=> "Île-de-France",
	  		"97"	=> "DOM-TOM"
	  	);

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
		$this->counters["gender"]["male"] = 0 ;
		$this->counters["gender"]["female"] = 0 ;

		$this->counters["departements"] = array();

		$this->counters["birthdate"] = array();
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

				$region = $this->dep2region[$departement];
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


		function cmp($a, $b) {
    		return strcmp($a["region"], $b["region"]);
		}

		usort($this->contacts, "cmp");
	}

	public function export() {
		$csv = new parseCSV();

		$fields = array("Nom","Surnom","Date de naissance","Genre","Courriel","Téléphone","Rue", "Complément adresse 1", "Complément adresse 2", "Code postal", "Ville", "Pays", "Département", "Région");

		$csv->output('membersByRegion.csv', $this->contacts, $fields, ',');
	}
	

}