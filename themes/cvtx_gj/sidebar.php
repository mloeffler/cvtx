<?php
/**
 * Sidebar
 *
 * @package WordPress
 * @subpackage cvtx
 */
?>

<div id="sidebar">
<!--
	<nav>
		<?php wp_nav_menu(array('theme_location' => 'cvtx-menu', 'fallback_cb' => false, 'walker' => (has_nav_menu('cvtx-menu') ? new cvtx_walker() : ''))); ?>
	</nav>
-->
	<ul class="side">
		<?php if(function_exists('dynamic_sidebar')) {
			dynamic_sidebar('cvtx');
		}?>
	</ul>
</div>