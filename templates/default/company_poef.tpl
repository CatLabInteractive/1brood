<h2><?php echo $title; ?></h2>

<p><?=$about?></p>

<?php if (isset ($list_users)) { ?>

	<?php if ($formAction) { ?>	
		<form method="post" action="<?php echo $formAction; ?>">
	<?php } ?>
	
	<table class="data">
	<?php foreach ($list_users as $v) { ?>

		<tr>
			<td style="width: 33%;"><?php echo $v[0]; ?></td>
			<td style="width: 34%; text-align: center;">&euro;  <?php echo $v[1]; ?></td>
			<td style="text-align: right;">
				<input type="text" name="<?=$v[2]?>" style="width: 50px;" />
			</td>
		</tr>

	<?php } ?>

	<?php if ($formAction) { ?>

		<tr>
			<td colspan="3" style="text-align: right;"><button type="submit"><?php echo $submit; ?></button></td>
		</tr>
	
		</table>
		</form>
		
	<?php } else { ?>

		</table>

	<?php } ?>

<?php } else { ?>

	<p><?php echo $noUsers; ?></p>

<?php } ?>