<?php 
/*
* 	Ausgabe der Inhlate:
*	- archive > Shortcards
*	- single > default
*	((single-touren > siehe: single-touren.php))
*/

/* =============================================================== *\ 
	H1 bei Single
	H2 bei Archive
\* =============================================================== */ 
$h_tag = "";
if (is_singular()):
	$h_tag = "h1";
else:
	$h_tag = "h2";
endif;

/* =============================================================== *\ 
 	 Variablen für Touren-Archiv 
\* =============================================================== */ 
$bereich_html = ""; // handle für KiBe usw.
$bereich = "";
$my_bereich_class = "grid_item "; // Klasse für article
$tourdatum = "";
$kurzinfo = "";
$tourenleiter = "";

/* =============================================================== *\ 
 	 Inhalte aus Blocks an anderer Stellen ausgeben 
\* =============================================================== */ 
$blocks = parse_blocks( get_the_content() );
foreach ( $blocks as $block ) {              
	if('acf/touren-kurzinfo' === $block['blockName']){
		// Sektion usw.
		if((isset($block['attrs']['data']['bereich'])) && ($block['attrs']['data']['bereich']!="")): // Sektion usw.
			foreach($block['attrs']['data']['bereich'] as $my_bereich):
				$my_bereich_class .= strtolower($my_bereich) . " ";
				
				$my_bereich_span_class = strtolower($my_bereich);
				$my_bereich_span_class .= " chip";
				$bereich_html .= "<div class='" . $my_bereich_span_class . "'>" . $my_bereich . "</div>";
				$bereich .= $my_bereich_span_class . "";
			endforeach;
		endif;
		// Bergtour T2
		$kurzinfo = render_block($block);
	}
	if('acf/tourdatum' === $block['blockName']){		
		//Tourdatum
		$tourdatum = render_block($block);
	}
}

//Name Tourenleiter
$auth_first_name = get_the_author_meta( 'first_name');
$auth_last_name = get_the_author_meta( 'last_name');
$name_tourenleiter = "$auth_first_name $auth_last_name";
if(($auth_first_name == "") && ($auth_last_name == "")){
	$name_tourenleiter = get_the_author_meta( 'user_email');
}


/* =============================================================== *\ 
 	 Touren-Archiv 
\* =============================================================== */ 
if((get_post_type()=="touren") && (is_archive())): ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class($my_bereich_class); ?> data-temp-filter=".<?php echo $bereich; ?>">
		<div class="touren_short_card">
			
			<?php  if( $bereich_html !=""): ?>
				<div class="card_header chips_container centered"><?php echo $bereich_html; ?></div>
			<?php else: ?>
				<div class="card_header chips_container centered"></div>
				
			<?php endif; ?>
			
			<div class="card_main">	
	  			<?php echo $tourdatum;  // Datumsausgabe ?>
				<?php echo "<" . $h_tag . " class='entry-title'>";  // Titel verlinkt ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
				<?php echo "</" . $h_tag . ">";
				echo "<div class='tourenleiter'>" . get_current_tourenleiter_name(get_the_ID()) . "</div>"; //Tourenleiter ?>
			</div>
			
			<div class="card_footer">
				<?php echo $kurzinfo; ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="more_button" rel="bookmark">Mehr <i class="fa-solid fa-chevron-right"></i></a>
			</div>
						
			<?php if ( !is_search() ) get_template_part( 'entry-footer' ); ?>
		</div>
	</article>

<?php 
else: 
/* =============================================================== *\ 
 	 Default 
\* =============================================================== */ 
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
			<?php if ( is_singular() ) { echo '<h1 class="entry-title" itemprop="headline">'; } else { echo '<h2 class="entry-title">'; } ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
			<?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
			<?php edit_post_link(); ?>
			<?php if ( !is_search() ) { get_template_part( 'entry', 'meta' ); } ?>
		</header>
		<?php get_template_part( 'entry',  'content'  ); ?>
		<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
	</article>

<?php endif;