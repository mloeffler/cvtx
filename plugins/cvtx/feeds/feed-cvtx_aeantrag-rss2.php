<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>
<?php if(isset($_GET['cvtx_aeantrag_antrag'])) {
   $antrag = $_GET['cvtx_aeantrag_antrag'];
   $loop = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                                              'meta_key'   => 'cvtx_sort',
                                              'orderby'    => 'meta_value',
                                              'order'      => 'ASC',
                                              'nopaging'   => true,
                                              'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                          'value'   => $antrag,
                                                                          'compare' => '='))));
   }
   else { 
       global $wp_query;
       $loop = $wp_query;
   }
?>

<channel>
	<title><?php if(isset($antrag)) printf(__('Amendments to %s','cvtx'),get_the_title($antrag)); else bloginfo_rss('name'); wp_title_rss();?></title>
	<atom:link href="<?php if(isset($antrag)) echo get_permalink($antrag); else self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php if(isset($antrag)) echo get_permalink($antrag); else self_link();  ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php while($loop->have_posts()): $loop->the_post(); ?>
	<?php $cvtx_before_content = get_cvtx_rss_before_content($post,'cvtx_aeantrag'); 
	      $cvtx_after_content  = get_cvtx_rss_after_content($post,'cvtx_aeantrag'); ?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<comments><?php comments_link_feed(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><?php echo get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true); ?></dc:creator>
		<?php the_category_rss('rss2') ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) { ?>
		<description><![CDATA[<?php print $cvtx_before_content; the_excerpt_rss(); print $cvtx_after_content; ?>]]></description>
<?php } else { ?>
		<description><![CDATA[<?php print $cvtx_before_content; the_excerpt_rss(); ?>]]></description>
	<?php if ( strlen( $post->post_content ) > 0 ) { ?>
		<content:encoded><![CDATA[<?php print $cvtx_before_content; the_content_feed('rss2'); print $cvtx_after_content; ?>]]></content:encoded>
	<?php } else { ?>
		<content:encoded><![CDATA[<?php print $cvtx_before_content; the_excerpt_rss(); print $cvtx_after_content; ?>]]></content:encoded>
	<?php } ?>
<?php } ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'); ?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>