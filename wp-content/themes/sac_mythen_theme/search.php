<?php get_header(); ?>

<section id="content" role="main">
	
	<?php if ( have_posts() ) : ?>
		<header class="header">
			<h1 class="entry-title"><?php printf('Suchergebnisse für: %s', get_search_query() ); ?></h1>
		</header>
	
		<?php while ( have_posts() ) : ?> 
			<?php the_post(); ?>
			<?php get_template_part( 'entry' ); ?>
		<?php endwhile; ?>
	
		<?php get_template_part( 'nav', 'below' ); ?>
	
	<?php else : ?>
	
		<article id="post-0" class="post no-results not-found">
			<header class="header">
				<h2 class="entry-title">Leider nichts gefunden</h2>
			</header>
	
			<section class="entry-content">
				<p>Keine Suchergebnisse</p>
				<?php get_search_form(); ?>
			</section>
		</article>

	<?php endif; ?>

</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
