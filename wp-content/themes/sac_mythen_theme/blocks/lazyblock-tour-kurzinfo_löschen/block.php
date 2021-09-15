<?php
/* =============================================================== *\ 

 	 wird über acf block gelöst,
     dieser kann gelöscht werden. 

\* =============================================================== */ 
  


/**
 * Example Block Template.
 *
 * @var  array  $attributes Block attributes.
 * @var  array  $block Block data.
 * @var  string $context Preview context [editor,frontend].
 */

?>
<div class="my-test-block">
    <?php echo ( $attributes['schwierigkeit']['label'] ); ?>
</div>