<?php get_header(); ?>
<?php 
/* =============================================================== *\ 
 	 Archive-Template für 
	 - Default
	 - Touren 
\* =============================================================== */ 
?>

<section id="content" role="main">
	<header class="header">
		<?php
		/* =============================================================== *\ 
		   Titel 
		\* =============================================================== */ 

		if(get_post_type()=="touren"): ?>
				<h1 class="entry-title">Unsere nächsten Touren</h1>
		<?php else:
		/*if ( is_day() ) { printf( __( 'Daily Archives: %s', 'ulrich_digital' ), get_the_time( get_option( 'date_format' ) ) ); }
		elseif ( is_month() ) { printf( __( 'Monthly Archives: %s', 'ulrich_digital' ), get_the_time( 'F Y' ) ); }
		elseif ( is_year() ) { printf( __( 'Yearly Archives: %s', 'ulrich_digital' ), get_the_time( 'Y' ) ); }
		else { _e( 'Archives', 'ulrich_digital' ); }
		*/
		?>
	<?php endif; ?>
	
	<?php 
	/* =============================================================== *\ 
 	   Menu für Isotope
	\* =============================================================== */ 
	$sektions_gruppe ="";
	if ( get_post_type()=="touren" && have_posts() ) : 
		$sektions_gruppe_menu_array = array();
		$sektions_gruppe .= "<div class='chips_container my_isotope_filters button-group'>";
		
		while ( have_posts() ) : 
			the_post();
			if((get_post_type()=="touren") && (is_archive())): 
				$blocks = parse_blocks( get_the_content() );
				foreach ( $blocks as $block ) {    
					          
					if('acf/touren-kurzinfo' === $block['blockName']):
						if((isset($block['attrs']['data']['bereich'])) && ($block['attrs']['data']['bereich']!="")): // Sektion usw.
							foreach($block['attrs']['data']['bereich'] as $my_sektions_gruppe):
								array_push($sektions_gruppe_menu_array, $my_sektions_gruppe);
							endforeach;
						endif;
					endif;
					
				}//End foreach
			endif;
			
		endwhile;
	
		$sektions_gruppe_menu_array = array_unique( $sektions_gruppe_menu_array);		
		
		foreach($sektions_gruppe_menu_array as $menu_eintrag):
			$sektions_gruppe_class = strtolower($menu_eintrag);
			$sektions_gruppe .= "<button class='chip " . $sektions_gruppe_class . "' data-filter='." . $sektions_gruppe_class . "'>" . $menu_eintrag . "</button>";
		endforeach;
		
		$sektions_gruppe .= "</div>";// sektions_gruppe_menu
	endif; //get_post_type
	echo $sektions_gruppe;
	wp_reset_postdata();  
	?>
	</header>
	
	

	<?php 
	/* =============================================================== *\ 

	 	 Ausgabe 

	\* =============================================================== */ 
	
  	$args = array( 
		'post_type' => 'touren',
		'post_status' => 'publish',
		'meta_key' => 'current_tour_date',
		'orderby' => 'meta_value',
		'order' => 'ASC',    
 	);
	$custom_query = new WP_Query($args); 



	/* =============================================================== *\ 
 	   Touren
	\* =============================================================== */ 
		if("touren"==get_post_type()):
			
			if ($custom_query->have_posts()) : 
				while($custom_query->have_posts()) : 
					$custom_query->the_post(); 
					$post_id = get_the_ID();
					$meta = get_post_meta($post_id);
					/*
					echo "<pre>";
					print_r($meta);
					echo "</pre>"; 
					*/
					?>
				<?php endwhile;?> 
			<?php else : ?>
				<p>Keine Beiträge!</p>
			<?php endif;?>
			<?php wp_reset_postdata();?>
			
			
			<?php
			if ( $custom_query -> have_posts() ) : ?>
			<div class="overview grid">
				<div class="grid_sizer"></div>
				<?php
				while ( $custom_query -> have_posts() ) : 
					$custom_query -> the_post(); 
					$post_id = get_the_ID();
					get_template_part( 'entry' ); 
				endwhile; ?>
			</div>
			<?php 
		endif; //have_posts() 

		else:
			if(have_posts()):
				while(have_posts()):
					the_post(); ?>
					<h1><?php the_title(); ?></h1> 
					<?php
				endwhile;
			endif;
		endif;
	
	get_template_part( 'nav', 'below' ); ?>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
