<!--<h2>Menu</h2>-->
<ul>
	<?php foreach ($list_menu as $v) { ?>
		<li><a href="<?php echo $v[1]; ?>"><span><?php echo $v[0]; ?></span></a></li>
	<?php } ?>
</ul>

<ul style="margin-top: 10px;">
	<?php foreach ($list_menu2 as $v) { ?>
		<li><a href="<?php echo $v[1]; ?>"><span><?php echo $v[0]; ?></span></a></li>
	<?php } ?>
</ul>
