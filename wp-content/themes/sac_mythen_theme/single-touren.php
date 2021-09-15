<?php 
/* =============================================================== *\ 
 	 Template für Single-Tour 
\* =============================================================== */ 
/*
Blocks an anderer Stelle ausgeben
https://florianbrinkmann.com/bestimmte-gutenberg-bloecke-eines-beitrags-im-theme-an-anderer-stelle-ausgeben-6602/

*/
get_header();

/* =============================================================== *\ 
   Diverse Blocks einzeln abfragen und ausgeben,
   damit Reihenfolge garantiert bleibt
\* =============================================================== */ 
$bereich = ""; // Sektion usw.
$bereich_class = "";
$bereich_html = "";
$datum_content = ""; // Datum-Zeile inkl. Verschiebe-Daten etc.
$touren_short_content = ""; // Kurzinfo-Block
$touren_details = "";
$remaining_content = ""; // Restlicher Inhalt
$header_bild = ""; //headerbild
$touren_programm = "";
if ( have_posts() ) :  ?>
	<?php while ( have_posts() ) :
        the_post(); 
        
		$blocks = parse_blocks( get_the_content() );
		$iterator = 0;
		foreach ( $blocks as $block ) {              
			$iterator++;
			
			// Datum
            if('acf/tourdatum'=== $block['blockName']){ 
                $datum_content .= render_block($block);                
            
			// Kurzinfo
			}elseif('acf/touren-kurzinfo' === $block['blockName']){
                if( (isset($block['attrs']['data']['bereich'])) && ($block['attrs']['data']['bereich'] > 0)): // Sektion usw.
                    foreach($block['attrs']['data']['bereich'] as $my_bereich):
						$my_class = $my_bereich . " chip";
						$bereich_html .= '<div class="' . strtolower($my_class) . '">' . $my_bereich . '</div>';
                    endforeach;
                endif;
                $touren_short_content .= render_block($block);  
			
			// Headerbild
			}elseif(($iterator <= 3) && ("core/image" ==$block['blockName']) ){
				$header_bild = render_block($block);
			
			// Details              
			}elseif('acf/touren-details' === $block['blockName']){
				$touren_details .= "<h2>Programm</h2>";
				$touren_details .= render_block($block);
			
			// übriger Inhalt
			}else{
                $remaining_content .= render_block($block);      
            }
		}//End foreach
    endwhile;


    /* =============================================================== *\ 
     	 HTML-Ausgabe 
    \* =============================================================== */ 
    ?>        
    <section id="content" role="main">
		
		<div class="tour_header">
        	<?php if($bereich_html!=""):?>
            	<div class="chips_container"><?php echo $bereich_html; ?></div>
        	<?php endif; ?>
        	<?php echo apply_filters('the_content', $datum_content); ?>
        	<h1 class="title"><?php echo get_the_title(); ?></h1>
        	<div class="leitung">Leitung: <?php echo get_current_tourenleiter_name(get_the_ID()); ?></div>
        	<div class="touren_short_info"><?php echo $touren_short_content; ?></div>
		</div>
		
		<div class="tour_body">
			<?php 
			if($header_bild!=""):?><div class="header_image"><?php echo $header_bild; ?></div><?php endif; ?>
        	<div class="touren_details"><?php echo $touren_details; ?></div>
        	<div class="remaining_content"><?php echo $remaining_content; ?></div>
    	</div>
        <footer class="footer">
    		<?php //get_template_part( 'nav', 'below-single' ); ?>
    	</footer>

    </section>

<?php
endif; 
//get_sidebar(); ?>
<?php get_footer();?>
