<?php
/**
* Touren- Kurz-Info Block Template.
*
* @param   array $block The block settings and attributes.
* @param   string $content The block inner HTML (empty).
* @param   bool $is_preview True during AJAX preview.
* @param   (int|string) $post_id The post ID this block is saved to.
*/


$bereich = get_field('bereich');
$art_der_tour = get_field('art_der_tour');

$skala_berg_und_alpinwandern = get_field('skala_berg-_und_alpinwandern');
$skala_berg_und_hochtouren = get_field('skala_berg-_und_hochtouren');
$skala_bike = get_field('skala_bike');
$skala_skitour = get_field('skala_skitour');
$skala_schneeschuhtouren = get_field('skala_schneeschuhtouren');
$skala_klettern_uiaa = get_field('skala_klettern_uiaa');
$skala_klettern_franzosisch = get_field('skala_klettern_franzosisch');

$zeitbedarf_gesamt = get_field('zeitbedarf_gesamte_tour');
$zeitbedarf_aufstieg = get_field('zeitbedarf_aufstieg');
$zeitbedarf_abstieg = get_field('zeitbedarf_abstieg');
$hohenmeter_aufstieg = get_field('hohenmeter_aufstieg');
$hohenmeter_abstieg = get_field('hohenmeter_abstieg');

$schwierigkeit = "";
$mehrtagig = false;

//Überprüfen, ob Tourdatum vorhanden ist
$temp_tourdatum = "";
$blocks_tour = parse_blocks( get_the_content() );
foreach ( $blocks_tour as $block_tour ) {
	if ( 'acf/tourdatum' === $block_tour['blockName'] ) {
        $temp_tourdatum = $block_tour['attrs']['data']['tourdatum'];

	}
}


/* =============================================================== *\ 
 	 Weiche, eintägig oder mehrtägig 
\* =============================================================== */ 
global $mehrtagige_tour;
if($mehrtagige_tour!=NULL):
    $count_mtt = count($mehrtagige_tour); // array zählen
    if($count_mtt >0):
        $mehrtagig = true;
    else:
        $mehrtagig = false;
    endif;
endif;

//require_once( get_template_directory()  . '/template_parts/icons_art_der_tour.php');

/* =============================================================== *\ 
 	 Art der Tour Icon 
\* =============================================================== */ 
if($art_der_tour!=""){
	global ${"icon_" . $art_der_tour}; // $icon_bergtour // $icon_hochtour usw.
	//echo ${"icon_" . $art_der_tour};
}

/* =============================================================== *\ 
 	 Schwierigkeits-Skalen 
\* =============================================================== */ 
$schwierigkeits_array = array();
if($skala_berg_und_alpinwandern!=""){
	$schwierigkeit = "<span class='skala_berg_und_alpinwandern'>" . $skala_berg_und_alpinwandern['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}
if($skala_berg_und_hochtouren!=""){
	$schwierigkeit = "<span class='skala_berg_und_alpinwandern'>" . $skala_berg_und_hochtouren['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}
if($skala_bike!=""){
	$schwierigkeit = "<span class='skala_bike'>" . $skala_bike['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}

if($skala_skitour!=""){
	$schwierigkeit = "<span class='skala_berg_und_alpinwandern'>" . $skala_skitour['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}
if($skala_schneeschuhtouren!=""){
	$schwierigkeit = "<span class='skala_berg_und_alpinwandern'>" . $skala_schneeschuhtouren['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}
if($skala_klettern_uiaa!=""){	
	$schwierigkeit = "<span class='skala_berg_und_alpinwandern'>" . $skala_klettern_uiaa['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}
if($skala_klettern_franzosisch!=""){
	$schwierigkeit = "<span class='skala_berg_und_alpinwandern'>" . $skala_klettern_franzosisch['label'] . "</span>";
	array_push($schwierigkeits_array, $schwierigkeit);
}



/* =============================================================== *\ 
 	 Eintägig
     - Aufstiegs-String
     - Abstiegs-String 
\* =============================================================== */ 
//Zeitbedarf gesamt
if($zeitbedarf_gesamt):
    $zeitbedarf_gesamt = $zeitbedarf_gesamt . " h";
endif;
//Aufstieg
$aufstiegs_string = "";
if($hohenmeter_aufstieg):
    $aufstiegs_string .= $hohenmeter_aufstieg . " hm";
endif;

if($hohenmeter_aufstieg && $zeitbedarf_aufstieg):
    $aufstiegs_string .= "<span>&nbsp;/&nbsp;</span>";
endif;

if($zeitbedarf_aufstieg):
    $aufstiegs_string .= $zeitbedarf_aufstieg . " h";
endif;

// Abstieg
$abstiegs_string = "";
if($hohenmeter_abstieg):
    $abstiegs_string .= $hohenmeter_abstieg . " hm";
endif;

if($hohenmeter_abstieg && $zeitbedarf_abstieg):
    $abstiegs_string .= "<span>&nbsp;/&nbsp;</span>";
endif;

if($zeitbedarf_abstieg):
    $abstiegs_string .= $zeitbedarf_abstieg . " h";
endif;


/* =============================================================== *\ 

 	 Mehrtätgig
     - Tag n 
        - - Aufstiegs-String
        - - Abststiegs-String
    - Tag n+1

\* =============================================================== */ 
$mehrtagig_kurzinfo_output = "";
$mehrtagig_class = "";
if( have_rows('dauer_und_hohenmeter_rep') ):
	$mehrtagig_kurzinfo_output .= "<div class='chips_container is_multiday_container'>";
	$mehrtagig_class = "is_multiday";
        while( have_rows('dauer_und_hohenmeter_rep') ) : 
            the_row();

            $label_mehrtagig = get_sub_field('label');
            $zeitbedarf_gesamt_mehrtatgig = get_sub_field('zeitbedarf_gesamt');
            $zeitbedarf_im_aufstieg_mehrtagig = get_sub_field('zeitbedarf_im_aufstieg');
            $hoehenmeter_im_aufstieg_mehrtagig = get_sub_field('hohenmeter_im_aufstieg');
            $zeitbedarf_im_abstieg_mehrtagig = get_sub_field('zeitbedarf_im_abstieg');
            $hoehenmeter_im_abstieg_mehrtagig = get_sub_field('hohenmeter_im_abstieg');

            $mehrtagig_kurzinfo_output .= '<div class="chips_container">';
            
            if($label_mehrtagig):
                $mehrtagig_kurzinfo_output .= '<div class="chip day_item">' . $label_mehrtagig . '</div>';
            endif;

            // Zeitbedarf gesamt
            if($zeitbedarf_gesamt_mehrtatgig):
                global $icon_zeitbedarf_gesamt;
                $mehrtagig_kurzinfo_output .= '<div class="chip rich_chip zeitbedarf_gesamt">';
                $mehrtagig_kurzinfo_output .= '<div class="svg_container icon">' . $icon_zeitbedarf_gesamt . '</div>';
                $mehrtagig_kurzinfo_output .= '<span class="value">' . $zeitbedarf_gesamt_mehrtatgig .' h</span></div>';
            endif;
            
			
            // Aufstiegs-Output zusammensetzen
            if(($zeitbedarf_im_aufstieg_mehrtagig)||($hoehenmeter_im_aufstieg_mehrtagig)):
                global $icon_aufstieg;
                $mehrtagig_kurzinfo_output .= '<div class="chip rich_chip aufstieg">';
                $mehrtagig_kurzinfo_output .= '<div class="svg_container icon">' . $icon_aufstieg . '</div><span class="value">';
            endif;

            if($hoehenmeter_im_aufstieg_mehrtagig):
                $mehrtagig_kurzinfo_output .= $hoehenmeter_im_aufstieg_mehrtagig .' hm';
            endif;
                
            if(($zeitbedarf_im_aufstieg_mehrtagig)&&($hoehenmeter_im_aufstieg_mehrtagig)):
                $mehrtagig_kurzinfo_output .= " / ";
            endif;
            
            if($zeitbedarf_im_aufstieg_mehrtagig):
                $mehrtagig_kurzinfo_output .= $zeitbedarf_im_aufstieg_mehrtagig .' h';
            endif;
            
            if(($zeitbedarf_im_aufstieg_mehrtagig)||($hoehenmeter_im_aufstieg_mehrtagig)):
                $mehrtagig_kurzinfo_output .= '</span></div>';
            endif;
            
            //Abstiegs-Output zusammensetzen
            if(($zeitbedarf_im_abstieg_mehrtagig)||($hoehenmeter_im_abstieg_mehrtagig)):
                global $icon_abstieg;
                $mehrtagig_kurzinfo_output .= '<div class="chip rich_chip abstieg">';
                $mehrtagig_kurzinfo_output .= '<div class="svg_container icon">' . $icon_abstieg . '</div><span class="value">';
            endif;

            if($hoehenmeter_im_abstieg_mehrtagig):
                $mehrtagig_kurzinfo_output .= $hoehenmeter_im_abstieg_mehrtagig .' hm';
            endif;
                
            if(($zeitbedarf_im_abstieg_mehrtagig)&&($hoehenmeter_im_abstieg_mehrtagig)):
                $mehrtagig_kurzinfo_output .= " / ";
            endif;
            
            if($zeitbedarf_im_abstieg_mehrtagig):
                $mehrtagig_kurzinfo_output .= $zeitbedarf_im_abstieg_mehrtagig .' h';
            endif;
            
            if(($zeitbedarf_im_abstieg_mehrtagig)||($hoehenmeter_im_abstieg_mehrtagig)):
                $mehrtagig_kurzinfo_output .= '</span></div>';
            endif;

            $mehrtagig_kurzinfo_output .= "</div>"; //chip_container

    endwhile;
	$mehrtagig_kurzinfo_output .= "</div>";

endif; 


$id = 'touren_kurzinfo_' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

$className = 'touren_kurzinfo ';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}
?>


<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">

    <div class="chips_container <?php echo $mehrtagig_class; ?>">
        
		<?php if($temp_tourdatum!=""):?>
            <div class="icon_tour_type"><?php echo ${"icon_" . $art_der_tour}; ?></div>
        <?php endif;?>
		
        <?php if($schwierigkeit!=""):
			global $icon_schwierigkeit; ?>
			<?php if(is_single()): 
				$my_class = "rich_chip"; 
			else:
				$my_class = "";
			endif; ?>
			
			<div class="chip schwierigkeit <?php echo $my_class; ?>">
				<?php if(is_single()): ?>
					<div class="svg_container icon"><?php echo $icon_schwierigkeit; ?></div>
				<?php endif; ?>
            
				<span class="value"><?php
					$arr_length = count($schwierigkeits_array);
					foreach($schwierigkeits_array as $schwierigkeit_index => $schwierigkeit_value):
						if($schwierigkeit_index < ($arr_length-1)):
							$schwierigkeit_value = $schwierigkeit_value . "<span>&nbsp;|&nbsp;</span>";
						endif;
						echo $schwierigkeit_value;
					endforeach; ?>
				</span>
			</div>			
			
		
        <?php endif; ?>
		
		<?php if(is_single()): 
		
			?>
	            <?php if($zeitbedarf_gesamt!=""): ?>
	                <div class="chip rich_chip zeitbedarf_gesamt">
	                    <div class="svg_container icon"><?php global $icon_zeitbedarf_gesamt; echo $icon_zeitbedarf_gesamt; ?></div>
	                    <span class="value"><?php echo $zeitbedarf_gesamt; ?></span>
	                </div>
	            <?php endif; ?>
	            
	            <?php if($aufstiegs_string!=""): ?>
	                <div class="chip rich_chip aufstieg">
	                    <div class="svg_container icon"><?php global $icon_aufstieg; echo $icon_aufstieg; ?></div>
	                    <span class="value"><?php echo $aufstiegs_string; ?></span>
	                </div>
	            <?php endif; ?>
	            
	            <?php if($abstiegs_string!=""):?>
	                <div class="chip rich_chip abstieg">
	                    <div class="svg_container icon"><?php global $icon_abstieg; echo $icon_abstieg; ?></div>
	                    <span class="value"><?php echo $abstiegs_string; ?></span>
	                </div>
	            <?php endif; ?>
	    
				<?php if($mehrtagig == true): // mehrtagig
	        		echo $mehrtagig_kurzinfo_output; ?>
	    		<?php endif; // mehrtagig ?>
			
			<?php
		endif;  //is_single(); ?>
	</div> <?php // art_der_tour info chips ?>

</div>

