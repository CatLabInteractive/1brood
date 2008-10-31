<h2><?php echo $title; ?></h2>

<p><?php echo $about; ?></p>

<h2><?php echo $contactDetails; ?></h2>

<?php if (isset ($warning)) { ?>
	<p class="false"><?php echo $warning; ?></p>
<?php } ?>

<form action="<?php echo $formAction; ?>" method="post">

	<label>
		<span><?php echo $company; ?>:</span>
		<input name="company" value="<?php echo $company_value; ?>" type="text" />
	</label>

	<label>
		<span><?php echo $adres; ?>:</span>
		<input name="adres" value="<?php echo $adres_value; ?>" type="text" />
	</label>

	<label>
		<span><?php echo $postcode; ?>:</span>
		<input name="postcode" value="<?php echo $postcode_value; ?>" type="text" />
	</label>

	<label>
		<span><?php echo $gemeente; ?>:</span>
		<input name="gemeente" value="<?php echo $gemeente_value; ?>" type="text" />
	</label>
	
	<label>
		<span><?php echo $reminder; ?>:</span>
		<select name="reminder">
			<option value="-1"><?=$noReminder?></option>
			<?php for ($i = 0; $i < 24; $i ++) { ?>
				<?php if ($i < 10) { ?>
					<option value="<?=$i?>" <?=$i == $reminder_value ? 'selected="selected"' : ''?>>0<?=$i?>h00</option>
				<?php } else { ?>
					<option value="<?=$i?>" <?=$i == $reminder_value ? 'selected="selected"' : ''?>><?=$i?>h00</option>
				<?php } ?>
			<?php } ?>
		</select>
	</label>

	<button><?php echo $submit; ?></button>

</form>
