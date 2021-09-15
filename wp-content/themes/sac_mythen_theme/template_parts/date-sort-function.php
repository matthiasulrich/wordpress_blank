<?php 
/* =============================================================== *\ 
 	 Beschreibung 
\* =============================================================== */ 
$is_archive = false;
if(is_archive()==1){
	$is_archive = true;
}

$today = date("Ymd");
$content_markup = '';

$project_array = array(); // Array mit allen Posts. Wird gebraucht um alle Posts herauszufiltern, welche ausschliesslich vergangene Daten besitzen
$all_project_IDs = array(); // Array mit allen(!) Projekt-ID's
$project_array_future_date = array(); // Array mit ID's, welche Daten mit zukünftigen (+heute) Daten haben
$one_to_one_array = array(); // Array, welches jedem Aufführungsdatum die Post-ID zugewiesen hat. 
$expired_posts_array = array(); // Projekte, welches ausschliesslich vergangene Aufführungsdaten besitzt.
$posts_to_show_array = array(); // Projekte, welche angezeigt werden sollen.


/* =============================================================== *\ 
 	 Funktionen 
\* =============================================================== */ 
//vergangene Einträge aus array löschen
if(function_exists ('my_removeElementWithValue')==false){
	function my_removeElementWithValue($array, $key){
		global $today;
		$gestern = $today - 1;
		foreach($array as $subKey => $subArray){
			if($subArray[$key] <= $gestern){
				unset($array[$subKey]);
			}
		}
		return $array;
	}
}

//doppelte Einträge löschen
if(function_exists ('super_unique')==false){
	function super_unique($array,$key){
		$temp_array = [];
		foreach ($array as &$v) {
			if (!isset($temp_array[$v[$key]])){
				$temp_array[$v[$key]] =& $v;
			}
		}
		$array = array_values($temp_array);
		return $array;
	}	
}





/* =============================================================== *\ 
 	 Alle Posts auslesen 
	 - Aufführungs-Datum aufbereiten
	 - $one_to_one_array befüllen
	 - $project_array befüllen
\* =============================================================== */ 
$args = array('post_type' => array( 'touren' ),);
$query = new WP_Query( $args );
if ( $query->have_posts() ) : 
	while ( $query->have_posts() ) : 
		$query->the_post(); 
		$my_ID = get_the_ID();
		$blocks = parse_blocks( get_the_content() );
		$data_for_project_array =  array (
		  'my_post_ID' => get_the_ID(),
		);

		foreach($blocks as $block){

			if('acf/tourdatum' === $block['blockName']){	
				/*
				Ziel: Array mit ID und zukünftigem Tour-Datum
				Teilziel 1: Wenn verschiedene Daten vorhanden sind, das aktuell gültige Datum herausfiltern
				
				*/
				// Abfragen ob ausgewählte (angeklickte) Verschiebe-Daten vorhanden sind
				echo "<pre>";
				print_r($block['attrs']['data']);
				//var_dump($block);
				echo "</pre>";
				$my_block_data = $block['attrs']['data'];
				$tourdatum = render_block($block);
var_dump($tourdatum);
				if($my_block_data['verschiebe_daten_0_tour_verschoben_auf']!=""){
					echo "tour ist verschoben";
					echo ($my_block_data['verschiebe_daten_1_verschiebe_datum']);
				}
				
				
				
				
				
				
				
				
				
				
				
				
				
				$date = $block['attrs']['data']['tourdatum']; // 2020-03-14T15:10
				//$date = strstr($date_time_picker_value, 'T', true);  //2020-03-14T15:10
				//$time = strstr($date_time_picker_value, 'T', false); //2020-03-14T15:10
				//$remove = array('-');
				//$date = str_replace($remove, "", $date);				
				$my_subarray = array(
					"my_id" => $my_ID,
					"tourdatum" => $date
				);
				$one_to_one_array[] = $my_subarray;	
				$data_for_project_array['tour_daten'][] = $date;	
			}
			
			

			
			if ( 'lazyblock/auffuhrung' === $block['blockName'] ) {
				//Datum aus Datenpicker verfügbar machen
				$date_time_picker_value = $block['attrs']['date-time-picker']; // 2020-03-14T15:10
				$date = strstr($date_time_picker_value, 'T', true); //2020-03-14T15:10
				$time = strstr($date_time_picker_value, 'T', false); //2020-03-14T15:10
				$remove = array('-');
				$date = str_replace($remove, "", $date);				
				$my_subarray = array(
					"my_id" => $my_ID,
					"my_auffuehrung" => $date
				);
				$one_to_one_array[] = $my_subarray;	
				$data_for_project_array['auffuehrungs_daten'][] = $date;
			}
		} 
		$project_array[] = $data_for_project_array; ?>
	<?php endwhile; ?>
<?php endif; ?>
<?php  ?>
<?php 	
/* =============================================================== *\ 

 	 Aufführungsdaten im Project-Array Sortieren 

\* =============================================================== */ 

$temp_arr = array();
foreach($project_array as $project){
	array_multisort($project['tour_daten']);
	$temp_arr[]= $project;
}
$project_array = $temp_arr;
echo "<pre>";
print_r($project_array);
echo "</pre>";

?>

<?php
/* =============================================================== *\ 

- Im $expired_posts_array werden alle Posts aufgelistet, deren sämtliche Aufführungen in der Vergangenheit liegen. 

- Im $one_to_one_array alle Aufführungen entfernen, welche vergangen sind
- $one_to_one_array aufsteigend sortieren, doppelte Einträge löschen
- Die jetztigen Eigenschaften des $one_to_one_array:
   - Post-ID's, welche Aufführungen in der Zukunft haben
   - Post-ID's sind einmalig
   - Posts sind aufgrund Aufführungs-Datum aufsteigend sortiert und können ausgegeben werden. 

\* =============================================================== */ 
  
  
/* =============================================================== *\ 
   $expired_posts_array befüllen
\* =============================================================== */ 
foreach($project_array as $my_post){ // alle Posts durchsuchen
	$vorbei_counter = 0;
	$projekt_vorbei_id = $my_post["my_post_ID"];
	foreach($my_post['auffuehrungs_daten'] as $auffuehrungsdatum){ //alle Unterarrays Aufführungsdaten 
		if($auffuehrungsdatum<$today){
			$vorbei_counter++;
		}		
	}
	$auffuehrungen_pro_projekt = count($my_post["auffuehrungs_daten"]);
	if($auffuehrungen_pro_projekt == $vorbei_counter){
		$expired_posts_array[] = $projekt_vorbei_id; // Die Post-ID dem Array übergeben
	}
}


/* =============================================================== *\ 
	Vergangene Einträge anzeigen oder zukünftige Eintrage anzeigen
	Im $one_to_one_array alle Einträge entfernen, 
	Wenn es sich nicht um ein Archiv handelt:
	 	die in der Vergangenheit liegen
	Wenn es sich um ein Archiv handelt:
		die in der Zukunft liegen
\* =============================================================== */ 


foreach($project_array as $project_key => $project){ // Jedes Projekt
	$project_has_future_date = false; 
	foreach($project as $my_key => $auffuehrungen){ //jede Aufführung + und jede id
		if(is_array($auffuehrungen)==true){ //jede aufführung
			foreach($auffuehrungen as $auffuehrung){
				if($auffuehrung >= $today){
					$project_has_future_date = true;
					$future_date_ID = $project_array[$project_key]['my_post_ID'];
					$project_array_future_date[] = $future_date_ID;
				}
			}
		}else{
			$all_project_IDs[] = $auffuehrungen; // Projekt-ID dem Array hinzufügen
		}
	}
}



if($is_archive == true){
	$posts_to_show_array = array_diff($all_project_IDs, $project_array_future_date);
} else{
	foreach($one_to_one_array as $k => $auffuehrung){
		//Vergangene Aufführungen entfernen
		if($auffuehrung['my_auffuehrung']<$today){
			unset($one_to_one_array[$k]);
		}
	}	
}

/* =============================================================== *\ 
 	 $one_to_one_array sortieren 
\* =============================================================== */ 

usort($one_to_one_array, function ($a, $b) {
    return $a['my_auffuehrung'] <=> $b['my_auffuehrung'];
});


/* =============================================================== *\ 
 	 $one_to_one_array – doppelte Einträge löschen 
\* =============================================================== */ 
 $one_to_one_array = super_unique($one_to_one_array, 'my_id');
 
/* =============================================================== *\ 
 	 ids dem $posts_to_show_array übergeben 
\* =============================================================== */ 
 if(is_archive()==false){
	 foreach($one_to_one_array as $post_to_show){
	 	$posts_to_show_array[] = $post_to_show['my_id'];
 	}
}
?>

