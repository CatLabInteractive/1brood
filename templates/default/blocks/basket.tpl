<?php if (isset ($list_products)) { ?>

	<h2><?php echo $title; ?></h2>
	<p>Klik om te verwijderen</p>

	<ul>
	<?php foreach ($list_products as $v) { ?>

		<li>
			<?php echo $v[1]; ?> x <a href="<?php echo $v[7]; ?>" title="<?php echo $v[6]; ?>" onclick="return confirm ('<?php echo $remove; ?>');"><?php echo $v[0]; ?></a>
		</li>

	<?php } ?>
	</ul>

<?php } ?>