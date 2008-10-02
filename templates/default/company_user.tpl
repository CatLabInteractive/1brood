<script type="text/javascript">
function checkUserStatusField(element)
{
	if (element.value == 'administrator')
	{
		alert ('<?php echo $adminWarning; ?>');
	}
}
</script>

<h2><?php echo $title; ?></h2>

<?php if (isset ($list_users)) { ?>

	<?php if ($formAction) { ?>	
		<form method="post" action="<?php echo $formAction; ?>">
	<?php } ?>
	
	<table class="data">
	<?php foreach ($list_users as $v) { ?>

		<tr>

			<td><?php echo $v[0]; ?></td>
			
			<?php if (isset ($statuses)) { ?>

				<td style="text-align: right;">
					<select name="<?php echo $v[3]; ?>" style="display: inline;" onchange="checkUserStatusField(this)">
					<?php foreach ($statuses as $stat => $status) { ?>
						<?php if ($stat == $v[2]) { ?>
							<option value="<?php echo $stat; ?>" selected="selected"><?php echo $status; ?></option>
						<?php } else { ?>
							<option value="<?php echo $stat; ?>"><?php echo $status; ?></option>
						<?php } ?>
					<?php } ?>
					</select>
				</td>

			<?php } else { ?>

				<td style="text-align: center; width: 25%;"><?php echo $v[1]; ?></td>

			<?php } ?>

		</tr>

	<?php } ?>

	<?php if ($formAction) { ?>

		<tr>

			<td colspan="2" style="text-align: right;"><button type="submit"><?php echo $submit; ?></button></td>

		</tr>
	
		</table>
		</form>
		
	<?php } else { ?>

		</table>

	<?php } ?>

<?php } else { ?>

	<p><?php echo $noUsers; ?></p>

<?php } ?>