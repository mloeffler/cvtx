<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
		<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
	</head>
	<body>
		<table style="width:94%;border-radius:3px;margin-left:3%;margin-top:0.7em">
			<tr><td id="header" style="height:150px; border-radius:10px;">
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
				<div id="b90" style="position:absolute; right:8%; top:-10px"></div>
				</div>
			</td></tr>
		</table>
		<table style="background:white;width:94%;border-radius:3px;margin-left:3%;margin-top:1em">
			<tr><td style="padding:15px"><?php print $content; ?></td></tr>
		</table>
	</body>
</html>
