<?php
/**
 * Mail-Template
 *
 * Used for HTML-Output of cvtx-sended mails
 *
 * @package WordPress
 * @subpackage cvtx
 */
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
		<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
	</head>
	<body>
		<table style="width:94%;border-radius:3px;margin-left:3%;margin-top:0.7em">
			<tr><td id="header" style="height:150px; border-radius:5px;" class="mail">
				<table id="verlauf" style="height:150px; border-radius:10px; background-position: center">
					<tr>
						<td id="headerimg" style="margin-left:40px; margin-top:30px">
							<h1><a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a></h1>
						</td>
					</tr>
					<tr>
						<td class="description" style="padding-left:40px"><?php bloginfo('description'); ?></td>
     				</tr>
     			</table>
				<div class="mail" style="position:absolute;right:8%;top:100px;background: url(<?php echo get_template_directory_uri(); ?>/images/b90_small.png); width: 80px; height: 42px;"></div>
				</div>
			</td></tr>
		</table>
		<table style="background:white;width:94%;border-radius:5px;margin-left:3%;margin-top:1em;box-shadow: 0px 0px 15px rgba(0,0,0,0.3);
	-webkit-box-shadow: 0px 0px 15px rgba(0,0,0,0.3);
	-moz-box-shadow: 0px 0px 15px rgba(0,0,0,0.3)" class="mail-content">
			<tr><td style="padding:15px"><?php print $content; ?></td></tr>
		</table>
	</body>
</html>
