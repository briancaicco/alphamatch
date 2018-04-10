<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $max_total && (( $max_total * 90 )/ 100 ) <= $total_count  ) {
	$class = ' class="col-sm-12 col-md-6 text-right fep-font-red"';
} else {
	$class = 'class="col-sm-12 col-md-6 text-right"';
}

?>

<div id="fep-wrapper">
	<div id="fep-header">
		<div class="row">
			<div class="col-sm-12">
				<div id="fep-menu">
					<?php do_action('fep_menu_button'); ?>
				</div><!--#fep-menu -->
			</div>
		</div>
		<div class="row" style="padding: 20px 0;">
  			<div class="col-sm-12 col-md-6">
  				<?php _e('You have', 'front-end-pm');
				?> <span class="fep_unread_message_count_text"><?php printf(_n('%s message', '%s messages', $unread_count, 'front-end-pm'), number_format_i18n($unread_count) ); 
				?></span> <?php 
				_e('and', 'front-end-pm');
				?> <span class="fep_unread_announcement_count_text"><?php
				printf(_n('%s announcement', '%s announcements', $unread_ann_count, 'front-end-pm'), number_format_i18n($unread_ann_count) ); 
				?></span> <?php _e('unread', 'front-end-pm'); ?>
			</div>
			<div <?php echo $class; ?> >

				<?php 
				_e('Message box size', 'front-end-pm');?>: <?php 
				printf(__('%1$s of %2$s', 'front-end-pm'),
				'<span class="fep_total_message_count">' . number_format_i18n($total_count) . '</span>',
				$max_text ); ?>
			</div>
		</div>

	  		<?php do_action('fep_header_note', $user_ID); ?>
	  
		</div>
