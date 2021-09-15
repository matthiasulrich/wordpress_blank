<?php
/* =============================================================== *\ 
 	 Block Template für Tour-Datum 
\* =============================================================== */ 

// Tourdatum 
/* Tourdatum in menschenfreundliche Lesbarkeit umwandeln */
$tourdatum = get_field('tourdatum');

// Mehrtägige Tour
global $mehrtagige_tour;

$mehrtagige_tour = get_field('mehrtagige_tour');

// Rückreise-Datum
if($mehrtagige_tour):
    $ruckreise_datum = get_field('rueckreise_datum');  
else:
    $ruckreise_datum = "";
endif;

// Durchführung 
$durchfuehrung_val = "";
$durchfuehrung = get_field('durchfuhrung');
if($durchfuehrung){
	$durchfuehrung_val = $durchfuehrung['label'];
}
$tourdatum_class = "";

$tour_cancelled = false;
if($durchfuehrung_val=="Tour absagen"){
    $tour_cancelled = true;
    $tourdatum_class .= "cancelled";
}
/* =============================================================== *\ 
 	 Verschiebe-Daten 
	 
	 - Auslesen und nach Datum sortieren
	 - Wenn «Verschoben auf» gewählt wurde, 
	 - - div.current_moving_date befüllen
	 - - Class an Tourdatum anhängen (durchstreichen)
\* =============================================================== */ 
  
$verschiebe_daten = "";
$current_moving_date="";
$repeater = get_field('verschiebe_daten');
$repeater_clean = array();
$order = array();
$mehrere_verschiebe_daten = false;
$tour_ist_verschoben = false;

if($repeater):

    //leere Felder aus dem Repeater löschen
    foreach($repeater as $repeater_field):
        if($repeater_field['verschiebe_datum']!==""):
            array_push($repeater_clean, $repeater_field);
        endif;
    endforeach;
    $repeater = $repeater_clean;

    // Repeater nach Datum sortieren
    foreach( $repeater as $i => $row ) {	
        $order[ $i ] = $row['verschiebe_datum'];
    }
    array_multisort( $order, SORT_ASC, $repeater );
    $count_verschiebe_daten_rep = count($repeater);

    // ul + li generieren
    if($count_verschiebe_daten_rep > 1):
        $mehrere_verschiebe_daten = true;
        $verschiebe_daten .= "<div class='label'>Verschiebe-Daten:</div><ul class='verschiebe_daten'>";
    else:
        $mehrere_verschiebe_daten = false;
        $verschiebe_daten .= "<div class='label'>Verschiebe-Datum:</div><ul class='verschiebe_daten'>";
    endif;
	
    foreach( $repeater as $i => $row ): 
		// Verschiebe-Daten
		$verschiebe_datum = $row['verschiebe_datum'];
		$verschiebe_daten .= "<li>";
		$verschiebe_daten .= make_human_date($verschiebe_datum);
		if (--$count_verschiebe_daten_rep > 0) { //Divider anhängen, ausser beim letzten Element
        	$verschiebe_daten .=" | ";
    	}
		$verschiebe_daten .= "</li>";
		
		
		// Checkbox «Tour verschoben auf»
		$tour_verschoben_auf = $row['tour_verschoben_auf'];
		if (sizeof($tour_verschoben_auf) > 0) {
			$tour_ist_verschoben = true;
			$tourdatum_class .= "cancelled ";
            $tourdatum_class .= "moved ";
		}
	endforeach; 

	$verschiebe_daten .= "</ul>";
endif; ?>




<?php 
/* =============================================================== *\ 
 	 Single-Page 
\* =============================================================== */ 
?>
<div class="date_row">
<?php if($tour_ist_verschoben == true): //Verschoben ?>
	<div class='current_moving_date'><i class="fas fa-exclamation-circle"></i> Verschoben auf: <?php echo make_human_date($verschiebe_datum); ?></div>
<?php endif;?>

<?php if($tour_cancelled == true): // Abgesagt ?>
    <span class="cancelled alert"><i class="fas fa-exclamation-circle"></i> Abgesagt</span>
<?php endif; ?>

<span class="datum <?php echo $tourdatum_class; ?>"><?php echo make_human_date($tourdatum); ?>
    <?php if($ruckreise_datum):
        echo " – " . make_human_date($ruckreise_datum);
    endif; ?>
</span>
<?php 
if(is_single()):
    if(($mehrere_verschiebe_daten == true) || ( $tour_ist_verschoben == false)): ?>
        <div class="verschiebe_daten_container"><?php echo $verschiebe_daten; ?></div>
    <?php endif; ?>
<?php endif; ?>
</div>








