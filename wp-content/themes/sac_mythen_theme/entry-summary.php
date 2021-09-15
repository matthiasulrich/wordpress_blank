<section class="entry-summary">
	
	<?php if(get_post_type()=="touren"): 
		
		echo "Touren";
	endif; ?>
	<?php the_excerpt(); ?>
	<?php if( is_search() ) { ?><div class="entry-links"><?php wp_link_pages(); ?></div><?php } ?>
</section>