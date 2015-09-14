<h2><?php echo $title; ?></h2>

<ul>
<?php foreach ($list_orders as $v) { ?>

	<li>
		<?php echo $v[0]; ?>, <?php echo $v[1]; ?> <br />
		<a href="<?php echo $v[3]; ?>">
			<?php echo $v[2]; ?>
		</a>
	</li>

<?php } ?>
</ul>