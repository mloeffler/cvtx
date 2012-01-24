<?php
/**
 * Reader-Template
 *
 * @package WordPress
 * @subpackage cvtx
 */
?>

<?php get_header(); ?>
	<div class="inner">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'kubrick') . '</p>'); ?>
				
				In dieser Antragsmappe sind enthalten:
				
                <?php
                $post_bak = $post;
                // get objects in reader term
                $items = array();
                $query = new WP_Query(array('taxonomy' => 'cvtx_tax_reader',
                                            'term'     => 'cvtx_reader_'.intval($post->ID),
                                            'orderby'  => 'meta_value',
                                            'meta_key' => 'cvtx_sort',
                                            'order'    => 'ASC',
                                            'nopaging' => true));
                while ($query->have_posts()) {
                    $query->the_post();
                    $items[] = $post->ID;
                }

                // list all contents
                $query  = new WP_Query(array('post_type' => array('cvtx_top', 'cvtx_antrag', 'cvtx_aeantrag'),
                                             'orderby'   => 'meta_value',
                                             'meta_key'  => 'cvtx_sort',
                                             'order'     => 'ASC',
                                             'nopaging'  => true));
                echo '<ul class="reader_list">';
                $open_top = false;
                $open_antrag = false;
                while($query->have_posts()):$query->the_post();
                    $type = get_post_type_object(get_post_type())->name;
                    if($type == 'cvtx_top') {
                        if($open_antrag) echo '</ul></li>';
                     	if($open_top) echo '</ul></li>';
                		echo '<li><h4>'; the_title(); echo '</h4><ul>';
                		$open_top = true; $open_antrag = false;
                	}
                	if($type == 'cvtx_antrag') {
                	    if($open_antrag) echo '</ul></li>';
                		echo '<li><a href="';the_permalink();echo '">'; the_title(); echo '</a><ul>';
                		$open_antrag = true;
                	}
                	if($type == 'cvtx_aeantrag') {
                		echo '<li><a href="';the_permalink();echo '">'; the_title(); echo '</a></li>';
                	}
				endwhile;
            
                wp_reset_postdata();
				$post = $post_bak; ?>
					<?php if($open_top) echo '</ul></li>'; if($open_antrag) echo '</ul></li>'; ?>
				</ul>
				<?php if ($file = cvtx_get_file($post, 'pdf')): ?>
					<h3>Download</h3>
					<?php the_title(); ?> <a href="<?php echo $file; ?>">herunterladen</a>!
				<?php endif; ?>

			</div>
			<p class="postmetadata alt">
				<small>
				<?php printf(__('Dieser %1$s wurde am %2$s um %3$s eingestellt.'),get_post_type_object(get_post_type())->labels->singular_name, get_the_time(__('l, j. F Y')), get_the_time(), get_the_category_list(', ')); ?>
				</small>
			</p>
		</div>
	<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.', 'kubrick'); ?></p>
	<?php endif; ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>