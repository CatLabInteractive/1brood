<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl">

	<head>
	
		<?php if (defined ('GOOGLE_ANALYTICS')) { ?>
			<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
			</script>
			<script type="text/javascript">
			_uacct = "<?php echo GOOGLE_ANALYTICS; ?>";
			urchinTracker();
			</script>
		<?php } ?>
		
		<title><?php echo $title; ?></title>

		<meta name="author" content="Thijs Van der Schaeghe"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link rel="stylesheet" type="text/css" href="<?=ABSOLUTE_URL?>css/<?=$template_name?>/layout.css" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?=ABSOLUTE_URL?>css/default/printer.css" media="print"/>

		<script src="<?=ABSOLUTE_URL?>js/common.js" type="text/javascript"></script>

	</head>
	
	<body>
	
		<div id="container">
		
			<h1><?php echo $title; ?></h1>

			<div id="left">
		
				<div id="menu">
					<?php echo $menu; ?>
				</div>

				<?php if (!empty ($basket)) { ?>
					<div id="basket" class="noPrint" style="clear: left;">
						<?php echo $basket; ?>
					</div>
				<?php } ?>

				<div id="login">
					<?php echo $login; ?>
				</div>
				
				<div style="padding-bottom: 60px;">&nbsp;</div>
				
				<div id="sponsor">
					<!-- Sponsor! -->
					<p style="position: absolute; bottom: 70px; font-size: 9px;">Powered by<br />
						<a href="http://www.nomadesk.com/" target="_BLANK">
							<img src="<?=IMAGE_URL?>Nomadesk-Logo.jpg" style="border: none;" />
						</a>
					</p>
				</div>
				
			</div>

			<div id="right">
				<div id="content">
					<?php echo $content; ?>
				</div>
			</div>
			
			<div id="footer">
				<p style="text-align: center;">
					<?php echo $footer; ?>
				</p>
				
				<p style="float: right; margin: 0px; line-height: 1.4; margin-top: 12px;">
					MySQL: <?=$mysqlCount?>
				</p>
				
				<p style="margin: 0px; line-height: 1.4;">
					<?php
					
						$o = $languages . ': ';
						foreach ($list_languages as $v)
						{
							$o .= '<a href="'.$v[1].'">'.$v[0].'</a> - ';
						}
						$o = substr ($o, 0, -3);

						$o .= '<br />' . $layouts . ': ';

						foreach ($list_layouts as $v)
						{
							$o .= '<a href="'.$v[1].'">'.$v[0].'</a> - ';
						}
						$o = substr ($o, 0, -3);

						echo $o;

					?>
				</p>
			</div>

			<div id="printFooter">

				<p><?=$printerFooter?></p>

			</div>
	
	</body>

</html>
