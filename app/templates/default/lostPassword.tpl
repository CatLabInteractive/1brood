<h2><?=$lostpass?></h2>

<?php if (isset ($done)) { ?>

	<p class="true"><?=$done?></p>

<?php } else { ?>

	<?php if (isset ($error)) { ?>
		<p class="false"><?=$error?></p>
	<?php } ?>

	<p><?=$about?></p>

	<form method="post" action="<?=$action?>">
		<label>
			<?=$email?>:<br />
			<input type="text" name="email" />
		</label>
		<button type="submit"><?=$submit?></button>
	</form>
	
<?php } ?>
