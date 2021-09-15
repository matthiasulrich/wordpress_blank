<section class="entry-content">
	<?php //
	/* =============================================================== *\ 
		Content-Ausgabe bei Touren-Übersicht 
	\* =============================================================== */ 
	if(is_archive() && get_post_type()=="touren"):
		$content_markup = "";
		$blocks = parse_blocks( get_the_content() );
		foreach ( $blocks as $block ) {    
				  
			if('acf/touren-kurzinfo' === $block['blockName']){
				$content_markup .= render_block( $block );

			}elseif('acf/tourdatum' === $block['blockName']){
				$content_markup .= render_block( $block );
			}
		}//End foreach
		echo $content_markup;
	
	else:
	/* =============================================================== *\ 
 	   Content-Ausgabe Default 
	\* =============================================================== */ 
		the_content(); 
	endif;
	?>
	<div class="entry-links"><?php wp_link_pages(); ?></div>
</section>