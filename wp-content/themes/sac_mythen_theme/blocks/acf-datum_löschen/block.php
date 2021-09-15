<?php
/* =============================================================== *\ 
 	 Block Template für Datum 
\* =============================================================== */ 
/*
* @param   array $block The block settings and attributes.
* @param   string $content The block inner HTML (empty).
* @param   bool $is_preview True during AJAX preview.
* @param   (int|string) $post_id The post ID this block is saved to.
*/

$tourdatum = get_field('tourdatum');
?>

<div class="datum"><?php echo "halo" . $tourdatum; ?></div>