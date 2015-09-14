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

			<label>
				<?php echo $email; ?>:<br />
				<input type="text" name="email" />
			</label>

		
			<label>
				<?php echo $password; ?>:<br />
				<input type="password" name="password" />
			</label>
		
			<label>
				<button type="submit"><?php echo $submit; ?></button>
			</label>
		
	</form>
	
	<p style="margin-top: 10px; text-align: center; clear:left;">
		<a href="<?=$lostpass_url?>"><?=$lostpass?></a>
	</p>

<?php } ?>
