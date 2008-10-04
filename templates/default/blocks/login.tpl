<?php if ($isLogin) { ?>

	<h2><?php echo $login; ?></h2>
	<p>

		<?php echo $myName; ?><br />
		<a href="<?php echo $logout_url; ?>"><?php echo $logout; ?></a>
		
	</p>

<?php } else { ?>

	<h2><?php echo $login; ?></h2>

	<?php if (isset ($warning)) { ?>
		<p class="false">

			<?php echo $warning; ?>

		</p>
	<?php } ?>
	
	<form action="<?php echo $loginAction; ?>" method="post">

		<div>
			<label>
				<?php echo $email; ?>:<br />
				<input type="text" name="email" />
			</label>
		</div>

		
		<div>
			<label>
				<?php echo $password; ?>:<br />
				<input type="password" name="password" />
			</label>
		</div>
		
		<div>
			<label>
				<button type="submit"><?php echo $submit; ?></button>
			</label>
		</div>
		
	</form>
	
	<p style="margin-top: 10px; text-align: center;">
		<a href="<?=$lostpass_url?>"><?=$lostpass?></a>
	</p>

<?php } ?>
