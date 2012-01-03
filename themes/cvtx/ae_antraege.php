<?php
/*
Template Name: &Auml;nderungsantrags&uuml;bersicht
*/
?>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/antragsmappe.css" type="text/css" media="screen" />
<?php wp_head(); ?>
</head>
<body>

<div id="header"><div id="verlauf"><p><a href="<?php bloginfo('url'); ?>"><< Zur&uuml;ck zur Seite</a> <span class="right"><?php bloginfo('name'); ?></span></p></div></div>

<?php
$antragstext = (isset($_POST['antragstext']) ? $_POST['antragstext'] : false);
$leer 		 = (isset($_POST['leer']) ? $_POST['leer'] : false);
$verfahren	 = (isset($_POST['verfahren']) ? $_POST['verfahren'] : false);
$ergebnis	 = (isset($_POST['ergebnis']) ? $_POST['ergebnis'] : false);
$antraege	 = (isset($_POST['antraege']) ? $_POST['antraege'] : false);
if($antragstext || $leer || $verfahren || $ergebnis || $antraege) $hide = true;
else $hide = false;

// TOP-Query
$loop = new WP_Query(array('post_type' => 'cvtx_top',
                           'orderby'   => 'meta_value',
                           'meta_key'  => 'cvtx_sort',
                           'nopaging'  => true,
						   'order'     => 'ASC'));
	if($loop->have_posts()):?>
	<div id="liste"><div class="toggler"><a href="#">Filter <?php if($hide) print 'anzeigen'; else print 'verbergen';?></a></div>
	<form method="post" id="filter" <?php if($hide) print 'style="display:none"'; ?> >
		<label for="tops">Tagesordnungspunkte und &Auml;nderungsantr&auml;ge</label>
		<select id="tops" style="width:100%" multiple="multiple" size="30" name="antraege[]">
		<?php
		while ($loop->have_posts()):$loop->the_post();
			$top_id = $post->ID;?>
			<optgroup label="<?php the_title(); ?>"> 
				<?php
				$loop2 = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                   			'orderby'   => 'meta_value',
                                   			'meta_key'  => 'cvtx_sort',
                                   			'nopaging'  => true,
											'order'      => 'ASC',
                                        	'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                    'value'   => $top_id,
                                                                    'compare' => '='))));
				while ($loop2->have_posts() ) : $loop2->the_post();?>
					<option value="<?php print $post->ID; ?>" label="<?php the_title(); ?>" 
						<?php if(isset($_POST['antraege']) && in_array($post->ID,$_POST['antraege'])) print 'selected="true"'?>>
						<?php the_title(); ?>
					</option>
				<?php endwhile;?>
			</optgroup>
		<?php endwhile;?>
		</fieldset>
	</select>
     <br />
     <input id="antragstext" name="antragstext" type="checkbox" <?php if($antragstext) print 'checked="true"'; ?>/>
     <label for="antragstext">Antragstext anzeigen</label>
     <input id="leer" name="leer" type="checkbox" <?php if($leer) print 'checked="true"'; ?>/>
     <label for="leer">Nur Antr&auml;ge mit &Auml;nderungsantr&auml;gen anzeigen</label>
     <input id="verfahren" name="verfahren" type="checkbox" <?php if($verfahren) print 'checked="true"'; ?>/>
     <label for="verfahren">Verfahren anzeigen</label>
     <input id="ergebnis" name="ergebnis" type="checkbox" <?php if($ergebnis) print 'checked="true"'; ?>/>
     <label for="ergebnis">Spalte f&uuml;r das Abstimmungsergebnis anzeigen</label>
     <p />
     <input type="submit" value="Liste anzeigen" />
	</form>
	</div>
	<?php endif; ?>

<?php if($antraege): ?>
	<div id="result">
	<?php foreach($antraege as $antrag_id):?>
		<?php $loop3 = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
        	    	                      'orderby'   => 'meta_value',
    	    	                          'meta_key'  => 'cvtx_sort',
	                	                  'nopaging'  => true,
                                          'order'      => 'ASC',
                                          'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                      'value'   => $antrag_id,
                                                                      'compare' => '=')))); ?>
        <?php if(!$leer || $loop3->have_posts()): ?>
			<?php $post = get_post($antrag_id); ?>
			<h3><?php the_title(); ?></h3>
			<?php if($antragstext) the_content(); ?>
	        <?php if($loop3->have_posts()): ?>
	        <table><tbody>
	        	<tr>
	        		<th>Zeile</th>
	        		<th>AntragstellerInnen</th>
	        		<th>Antrag</th>
	        		<?php if($verfahren):?><th>Verfahren</th><?php endif; ?>
	        		<?php if($ergebnis):?><th>Ergebnis</th><?php endif; ?>
	        	</tr>
	        	<?php while($loop3->have_posts()):$loop3->the_post();?>
	        		<tr>
	        			<td><?php print get_post_meta($post->ID,'cvtx_aeantrag_zeile',true); ?></td>
	        			<td><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller',true); ?></td>
	        			<td><?php the_content(); ?></td>
	        			<?php if($verfahren):?><td class="<?php print get_post_meta($post->ID, 'cvtx_aeantrag_verfahren',true); ?>">
	        				<?php print get_post_meta($post->ID,'cvtx_aeantrag_verfahren',true); ?></td><?php endif; ?>
	        			<?php if($ergebnis):?><td><?php print get_post_meta($post->ID,'cvtx_aeantrag_ergebnis',true); ?></td><?php endif; ?>
	        		</tr>
		        <?php endwhile; ?></tbody>
	    	</table>
	    	<?php endif;?>
	    <?php endif; ?>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/ae_antraege_script.js"></script>
<?php wp_footer(); ?>
</body>
</html>