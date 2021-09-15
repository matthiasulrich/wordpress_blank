<?php get_header(); ?>

<section id="content" role="main">
	<article id="post-0" class="post not-found">

		<section class="entry-content">
			<h1 class="entry-title">Hoppla!</h1>
			<p>Hier stimmt etwas nicht - die gesuchte Seite gibt es nicht.</p>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Hier</a> gehts zur Startseite.</p>
		<?php //get_search_form(); ?>
		</section>
	
	</article>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
