<?php 
/* =============================================================== *\ 

 	 Projekt-Ausgabe 

\* =============================================================== */ 

foreach($posts_to_show_array as $my_post):
	$post = get_post($my_post);
	$blocks = parse_blocks($post->post_content);
	?>
	<article id="post-<?php echo $my_post; ?>" <?php post_class($my_post); ?>>
        
        <?php 
        /* =============================================================== *\ 
 	       Bild ausgeben, wenn entspr. gekenntzeichnet
        \* =============================================================== */  
        foreach ($blocks as $block) {
            if(array_key_exists ('visibleOnArchive' , $block['attrs'])==true){
                if($block['blockName']=='core/image'){
                $content_markup = render_block( $block );
                echo $content_markup;
                }
            }
        }
        ?>
        
        <?php 
        /* =============================================================== *\ 
 	       Titel augeben
        \* =============================================================== */ 
        ?>
        
		<h2><a href="<?php echo get_the_permalink($my_post); ?>"><?php echo get_the_title($my_post); ?></a></h2>
		
		<?php
		/* =============================================================== *\ 
	 	 	Aufführungen im Projekt drin sortieren 
		\* =============================================================== */ 	
		array_multisort(array_map(function($element) {
			if(isset($element['attrs']['date-time-picker'])){
				return $element['attrs']['date-time-picker'];
			}
		}, $blocks), SORT_ASC, $blocks);	

        // Aufführungsblock ausgeben
		foreach ($blocks as $block) {
			if ( 'lazyblock/auffuhrung' === $block['blockName'] ) {
				$content_markup = render_block( $block );
				echo $content_markup;
			}
		}
        
        // Weitere Blocks, welche entsprechend gekenntzeichnet wurden, ebenfalls ausgeben
        foreach ($blocks as $block) {
            if((array_key_exists ('visibleOnArchive' , $block['attrs'])==true) &&($block['blockName']!='core/image')){
                $content_markup = render_block( $block );
                echo $content_markup;
            }
        }
        		
		wp_reset_postdata(); ?>
	</article>
<?php endforeach; ?>

</section>

