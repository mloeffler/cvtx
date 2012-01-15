			<div id="comments">
<?php if ( post_password_required() ) : // don't display comments for password-protected posts ?>
				<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'oenology' ); ?></p>
			</div><!-- #comments -->
<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<h2 class="commentsheader">Feedback</h2>

<?php if ( have_comments() ) : ?>

<?php
	$postrac = false; // Boolean (true/false) variable indicating if a post has Trackbacks or Pingbacks. Set to 'false' until determined to be true.
	if ($comments) { // if there are no comments, don't look for Trackbacks
	
		foreach ($comments as $comment) { // step through each comment
			if( get_comment_type() != "comment" ) { 
				$postrac = true;  // if a comment has a comment_type other than "comment" (i.e. a Trackback or Pingback), set $postrac to 'true'
				} 
			}
			
		if ( $postrac ) { // if the post has any trackbacks por pingbacks, display them as a list ?>
			<h3 class='trackbackheader'>Trackbacks</h3>
                        <ol class='trackbacklist'>
			<?php foreach ($comments as $comment) { // step through each comment
				if(get_comment_type() != "comment") { // if the comment is a Trackback or Pingback ?>
					<li><?php echo comment_author_link(); // display the Comment Author Link (the Trackback/Pingback URL) ?></li>
				<?php }
			} ?>
			</ol>
		<?php }
	}
?>

<h3>Comments <?php if ( ! comments_open() ) { ?> <small>(Comments are closed)</small><?php } ?></h3>

	

<?php $i = 0; ?>
	<span id="comments-responses" style="font-weight:bold;"><?php comments_number('No Responses', 'One Response', '% Responses' );?> to &#8220;<?php the_title(); ?>&#8221;</span>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // If the paged comments setting is enabled, and enough comments exisst to cause comments to be paged ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( '<span class="meta-nav">&larr;</span> Older Comments' ); ?></div>
				<div class="nav-next"><?php next_comments_link( 'Newer Comments <span class="meta-nav">&rarr;</span>' ); ?></div>
			</div> <!-- .navigation -->
<?php endif; // check for comment navigation 
		
		if ( get_comments_number() > '0' ) { ?>
			<ol class="commentlist">
				<?php	wp_list_comments( 'type=comment&avatar_size=40' ); ?>
			</ol>
		<?php }

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( '<span class="meta-nav">&larr;</span> Older Comments' ); ?></div>
				<div class="nav-next"><?php next_comments_link( 'Newer Comments <span class="meta-nav">&rarr;</span>' ); ?></div>
			</div><!-- .navigation -->
<?php endif; // check for comment navigation ?>

<?php else : // or, if we don't have comments:

endif; // end have_comments() 

comment_form(); 
?>

</div><!-- #comments -->
