<h2><?php echo $title; ?></h2>

<?php if (!empty ($message)) { ?>
	<div class="message">
		<?php echo $message; ?>
	</div>
<?php } ?>

<h3><?php echo $products; ?></h3>
<?php if (isset ($list_products)) { ?>
	<table class="productsInfo">
		<?php $last = count ($v); $i = 0; foreach ($list_products as $v) { $i ++;?>
			<tr class="prodMain">
				<td><?php echo $v[0]; ?></td>
				<td style="width: 15%;" class="price"><?php echo $currency . ' ' . $v[2]; ?></td>
			</tr>

			<tr class="prodDetail">
				<?php if ($i == $last) { ?>
					<td colspan="2" class="last"><?php echo $v[1]; ?></td>
				<?php } else { ?>
					<td colspan="2"><?php echo $v[1]; ?></td>
				<?php } ?>
			</tr>
			
			
		<?php } ?>
	</table>
<?php } ?>