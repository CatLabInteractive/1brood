<h2><?php echo $title; ?></h2>
<p><?php echo $about; ?></p>

<h2><?php echo $choose; ?></h2>
<?php if (isset ($list_companies)) { ?>

<form action="<?php echo $formAction; ?>" method="post" style="margin-bottom: 15px;">

	<label>

		<?php echo $chooseComp; ?>:<br />
		<select name="chooseCompany">
			<?php foreach ($list_companies as $v) { ?>
				<option value="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></option>
			<?php } ?>
		</select>

	</label>

	<button type="submit"><?php echo $submit; ?></button>

</form>
<?php } else { ?>
	<p><?php echo $noCompanies; ?></p>
<?php } ?>

<p><?php echo $add[0]; ?><a href="<?php echo $addUrl; ?>"><?php echo $add[1]; ?></a><?php echo $add[2]; ?></p>