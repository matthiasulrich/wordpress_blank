<section class="entry-meta">
	<span class="author vcard"><?php the_author_posts_link(); ?></span>
	<!-- oder 
	<span class="author vcard"> echo get_the_author_meta('display_name');</span>
	-->
	<span class="meta-sep"> | </span>
	<span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
</section>
