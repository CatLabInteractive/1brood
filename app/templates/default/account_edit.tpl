<?php $this->setTextSection ('edit', 'account'); ?>

<h2><?=$this->getText ('title')?></h2>

<?php if (isset ($done) && $done) { ?>
	<p class="true"><?=$this->getText ('done')?></p>
<?php } elseif (isset ($done) && !$done) { ?>
	<p class="false"><?=$this->getText ('error')?></p>
<?php } ?>

<form action="<?php echo $form_action; ?>" method="post">

	<label>
		<span><?=$this->getText ('firstname', 'register')?>:</span>
		<input name="firstname" value="<?php echo $firstname; ?>" type="text" />
	</label>
	
	<label>
		<span><?=$this->getText ('name', 'register')?>:</span>
		<input name="name" value="<?php echo $name; ?>" type="text" />
	</label>
	
	<label>
		<span><?=$this->getText ('nickname', 'register')?>:</span>
		<input name="nickname" value="<?php echo $nickname; ?>" type="text" />
	</label>
	
	<label>
		<span><?=$this->getText ('email', 'register')?>:</span>
		<input name="email" value="<?php echo $email; ?>" type="text" />
	</label>

	<button><?=$this->getText ('submit')?></button>

</form>
