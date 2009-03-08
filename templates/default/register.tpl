<h2><?php echo $title; ?></h2>

<p><?php echo $about; ?></p>

<h2><?php echo $contactDetails; ?></h2>

<?php if (isset ($warning)) { ?>
	<p class="false"><?php echo $warning; ?></p>
<?php } ?>

<form action="<?php echo $form_action; ?>" method="post">

	<label>
		<span><?php echo $firstname; ?>:</span>
		<input name="firstname" value="<?php echo $firstname_value; ?>" type="text" />
	</label>

	<label>
		<span><?php echo $name; ?>:</span>
		<input name="name" value="<?php echo $name_value; ?>" type="text" />
	</label>

	<label>
		<span><?php echo $email; ?>:</span>
		<input name="email" value="<?php echo $email_value; ?>" type="text" />
	</label>

	<label>
		<span><?php echo $password1; ?>:</span>
		<input name="password1" type="password" />
	</label>

	<label>
		<span><?php echo $password2; ?>:</span>
		<input name="password2" type="password" />
	</label>

	<button name="register" value="register"><?php echo $submit; ?></button>

</form>
