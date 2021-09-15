<?php get_header(); ?>

<?php echo "hallo optionen";
 print_r(get_option("ulrich_digital_options"));
?>
<main id="content">


<?php get_header(); ?>

		<div id="container">
			<div id="content" role="main">



<?php get_sidebar(); ?>
<?php get_footer(); ?>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php get_template_part( 'entry' ); ?>
	<?php comments_template(); ?>
	<?php endwhile; endif; ?>
	<?php get_template_part( 'nav', 'below' ); ?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
