<?php 
/* =============================================================== *\ 

 	 Block-Template für Touren-Details 
	 die Funktionen für die HTML-Ausgabe > functions.php 
\* =============================================================== */ 

/* ACF-Fields */

$karte_25000 = get_field('karte_25000'); // Array
$karte_50000 = get_field('karte_50000'); // Array
$karte_spez = get_field('karten_spez'); // Array
$programm = get_field('programm');
$mitnehmen_ausrustung = get_field('mitnehmen_ausrustung'); // Array
$treffpunkt = get_field('treffpunkt'); //Group
$verpflegung = get_field('verpflegung');
$durchfuhrung = get_field('durchfuhrung');
$teilehmerzahl = get_field('teilehmerzahl');
$kosten = get_field('kosten');
$bemerkung = get_field('bemerkung');
$anmeldung_und_auskunft = get_field('anmeldung_und_auskunft'); //Array

if(is_single()):
	/* =============================================================== *\ 
	 	 Programm 
	\* =============================================================== */ 
	?>
	<div class="details_container">
		<?php if($programm):?>
			<div class="linke_spalte"><?php echo single_tour_textfield('programm', $programm);?></div>
		<?php endif; ?>

	<div class="rechte_spalte"><?php
	/* =============================================================== *\ 
	 	 Karten 
	\* =============================================================== */ 
	if( ($karte_25000) || ($karte_50000) || $karte_spez ):
		$map_content = "";
		if($karte_25000):
			 $map_content .= "<div class='karte'>1:25 000, Blatt Nr.: ";
			 $map_content .= implode(', ', $karte_25000);
			 $map_content .= "</div>";
		endif;
		
		if($karte_50000):
			 $map_content .= "<div class='karte'>1:50 000, Blatt Nr.: ";
			 $map_content .= implode(', ', $karte_50000);
			 $map_content .= "</div>";
		endif;
		
		if($karte_spez):
			 $map_content .= "<div class='karte'>";
			 $map_content .= implode(', ', $karte_spez);
			 $map_content .= "</div>";
		endif;
		
		echo single_tour_array('karte',  $map_content);
	endif;


	/* =============================================================== *\ 
		Bemerkung 
	\* =============================================================== */ 
	if($bemerkung):
	   echo single_tour_textfield('bemerkung', $bemerkung);
	endif;


	/* =============================================================== *\ 
	 	 Mitnehmen/Ausrüstung 
	\* =============================================================== */ 
	if($mitnehmen_ausrustung):
		$my_arr = array();
	    foreach( $mitnehmen_ausrustung as $row ) {
	    	array_push($my_arr, $row['ausrustung']);    
	    }

		$content = "<ul><li>";
		$content .= implode("</li><li>", $my_arr);
		$content .= "</li></ul>";
		
		echo single_tour_array('mitnehmen_ausrustung',  $content);
	endif;  


	/* =============================================================== *\ 
	 	 Verpflegung 
	\* =============================================================== */ 
	if($verpflegung):
		echo single_tour_textfield('verpflegung', $verpflegung);
	endif;
	 
	/* =============================================================== *\ 
	 	 Durchführung 
	\* =============================================================== */ 
	if($durchfuhrung):
		echo single_tour_textfield('durchfuhrung', $durchfuhrung);
	endif;
	 

	/* =============================================================== *\ 
	 	 Treffpunkt 
	\* =============================================================== */ 
	//if($treffpunkt):
	if($treffpunkt['zeit'] || $treffpunkt['ort'] || $treffpunkt['bemerkung']!=""):
		$content = "";

		if(($treffpunkt['zeit']) || ($treffpunkt['ort'])):
			$content .= "<div class='zeit_ort'>";
		endif;

		if($treffpunkt['zeit']):
			$content .= $treffpunkt['zeit'] . " Uhr";
		endif;

		if(($treffpunkt['zeit']) && ($treffpunkt['ort'])):
			$content .= ", ";
		endif;

		if($treffpunkt['ort']):
			$content .= $treffpunkt['ort'];
		endif;

		if(($treffpunkt['zeit']) || ($treffpunkt['ort'])):
			$content .= "</div>";
		endif;
		
		if($treffpunkt['bemerkung']):
			$content .= "<div class='bemerkung'>" . $treffpunkt['bemerkung'] . "</div>";
		endif;
			
		echo single_tour_array('treffpunkt', $content);
	endif;


	/* =============================================================== *\ 
		Teilehmerzahl 
	\* =============================================================== */ 
	if($teilehmerzahl):
	   echo single_tour_textfield('teilnehmerzahl', $teilehmerzahl);
	endif;


	/* =============================================================== *\ 
	 	 Kosten
	\* =============================================================== */ 
	if($kosten):
	   echo single_tour_textfield('kosten', $kosten);
	endif;


	/* =============================================================== *\ 
	   Auskunfts-Feld 
	\* =============================================================== */ 
	if($anmeldung_und_auskunft):
		
		//Name Tourenleiter
		$anmeldung_output = "";
		$user_info = get_userdata(get_current_user_id());
		$anderer_name_anzeigen = $anmeldung_und_auskunft['anderer_name_oder_telefonnummer'];
		
		$anderer_name = get_current_tourenleiter_name(get_the_ID());
		
		// Anderer Name
		if($anderer_name_anzeigen!=""){
			$anderer_name = $anmeldung_und_auskunft['name'];
		}

		$anmeldung_output .= "<span>";
		$anmeldung_output .= $anderer_name;
		$anmeldung_output .= ",</span> ";
				
		//Telefonnummer
		global $authordata;
		$authordata_id = $authordata->ID;
		$my_author_id = "user_" . $authordata_id;
		$tel_nr = "";
		
		
		if($anderer_name_anzeigen ==""):
			if(get_field('telefonnummer', $my_author_id)):
				$tel_nr = get_field('telefonnummer', $my_author_id);
				$human_tel_nr = substr($tel_nr,0,3).' '.substr($tel_nr,3,3).' '.substr($tel_nr,6,2) . ' ' . substr($tel_nr, 8);
				if(substr($tel_nr,0,1)=="0" ){
					$int_tel_nr = "+41" . substr($tel_nr,1);
					
				}else{
					$int_tel_nr = "+41" . substr($tel_nr,0);
				}
				$anmeldung_output .='<a class="telefonnummer" href="tel:' . $int_tel_nr . '">' . $human_tel_nr . '</a>';
			endif;
		else:
			//Telefonnummer überschreiben
			$andere_telefonnummer = "";
			if($anderer_name_anzeigen!=""){
				$andere_telefonnummer = $anmeldung_und_auskunft['telefonnummer'];
				$human_tel_nr = substr($andere_telefonnummer,0,3).' '.substr($andere_telefonnummer,3,3).' '.substr($andere_telefonnummer,6,2) . ' ' . substr($andere_telefonnummer, 8);
				if(substr($andere_telefonnummer,0,1)=="0" ){
					$int_tel_nr = "+41" . substr($andere_telefonnummer,1);
					
				}else{
					$int_tel_nr = "+41" . substr($andere_telefonnummer,0);
				}
				$anmeldung_output .='<a class="telefonnummer" href="tel:' . $int_tel_nr . '">' . $human_tel_nr . '</a>';
			}
		endif;
	
		
		if($anmeldung_und_auskunft['anmeldung_moglich_bis']!=""):
			$temp_var = $anmeldung_und_auskunft['anmeldung_moglich_bis'];
			$anmeldung_output .= "<span>; </span><span>" . $temp_var . "</span>";
		endif;
		echo single_tour_textfield('anmeldung_und_auskunft', $anmeldung_output);
	endif;


	/* =============================================================== *\ 
	   "Anmeldung erforderlich"-Feld 
	\* =============================================================== */ 
	if($anmeldung_und_auskunft):  
	  	if($anmeldung_und_auskunft['anmeldung_ist_erforderlich']=='Anmeldung ist erforderlich'):
			$output = "Anmeldung ist erforderlich";
			echo single_tour_textfield('anmeldung_ist_erforderlich', $output);
		endif;
	endif;


	/* =============================================================== *\ 
	   "Hier anmelden"-Feld 
	\* =============================================================== */ 
	if($anmeldung_und_auskunft):
		if( ($anmeldung_und_auskunft['anmeldung_ist_erforderlich']=='Anmeldung ist erforderlich') && (get_field('telefonnummer', $my_author_id)) ):
	 		$link_text = "Hier telefonisch anmelden";
			$link_target = "tel";
			$link_url = $int_tel_nr;
	 		echo single_tour_buttton_field('hier_anmelden', $link_text, $link_target, $link_url);
	 	endif;
	endif; ?>
</div> <?php // end rechte spalte ?>
</div> <?php // end details_container
endif; //if is_single
?>