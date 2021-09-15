<?php get_header(); ?>

<section id="content" role="main">
	<header class="header">
		<h1 class="entry-title"><?php _e( 'Tag Archives: ', 'betschart-gh' ); ?><?php single_tag_title(); ?></h1>
	</header>
	
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?> 
			<?php the_post(); ?>
			<?php get_template_part( 'entry' ); ?>
		<?php endwhile; ?>
	<?php endif; ?>

	<?php get_template_part( 'nav', 'below' ); ?>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
