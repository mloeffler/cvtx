<div id="sidebar">
	<nav>
		<?php wp_nav_menu(array('theme_location' => 'cvtx-menu', 'fallback_cb' => false)); ?>
	</nav><div class="filler"></div>
	<ul class="side">
		<li id="first">
			<div class="filler2"></div>
			<div class="inner">
				Test
			</div>
		</li>
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
		<li id="about">
	  	<h2></h2>
	  	<p>This is my blog.</p>
		</li>
	<?php endif; ?>
	</ul>
</div>