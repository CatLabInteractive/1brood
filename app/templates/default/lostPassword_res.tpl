<h2><?=$title?></h2>

<?php if (isset ($success)) { ?>
	<p class="true"><?=$success?></p>
<?php } else { ?>

	<p><?=$about?></p>

	<?php if (isset ($warning)) { ?>
		<p class="false"><?=$warning?></p>
	<?php } ?>

	<form method="post" action="<?php echo isset($action) ? $action : '' ?>">
		<div>
			<label>
				<?=$pass1?>:<br />
				<input type="password" name="pass1" />
			</label>
		</div>
	
		<div>
			<label>
				<?=$pass2?>:<br />
				<input type="password" name="pass2" />
			</label>
		</div>	
	
		<button type="submit"><?=$submit?></button>
	</form>
<?php } ?>
