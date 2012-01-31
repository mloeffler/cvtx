<?php
/**
 * The Footer widget areas.
 *
 * @package WordPress
 * @subpackage cvtx_gj
 */
?>

<?php
	/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 * If none of the sidebars have widgets, then let's bail early.
	 */
	if (is_active_sidebar('cvtx_footer')):
?>
		<div id="first" class="widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar('cvtx_footer_first'); ?>
			</ul>
		</div><!-- #first .widget-area -->
<?php endif; ?>

<?php if(is_active_sidebar('cvtx_footer_second')): ?>
		<div id="second" class="widget-area">
			<ul>
				<?php dynamic_sidebar('cvtx_footer_second'); ?>
			</ul>
		</div>
<?php endif; ?>