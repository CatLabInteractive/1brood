<h2>Menu</h2>
<ul>
	<?php foreach ($list_menu as $v) { ?>
		<li>&raquo; <a href="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></a></li>
	<?php } ?>
</ul>

<ul>
	<?php foreach ($list_menu2 as $v) { ?>
		<li>&raquo; <a href="<?php echo $v[1]; ?>" target="_BLANK"><?php echo $v[0]; ?></a></li>
		<li>&raquo; <a href="<?php echo $v[1]; ?>" target="_BLANK"><?php echo $v[0]; ?></a></li>
	<?php } ?>
</ul>
