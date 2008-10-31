<?php $this->setTextSection ('poefboek', 'company'); ?>

<h2><?php echo $title; ?></h2>

<p><?=$about?></p>

<?php if (isset ($list_users)) { ?>

	<?php if ($formAction) { ?>	
		<form method="post" action="<?php echo $formAction; ?>">
	<?php } ?>
	
	<table class="data">
	
		<tr>
			<th style="text-align: left; width: 20%;"><?=$this->getText ('user')?></th>
			<th style="width: 20%;"><?=$this->getText ('saldo')?></th>
			<th style="width: 40%;"><?=$this->getText ('comments')?></th>
			<th style="text-align: right;"><?=$this->getText ('add')?></th>
		</tr>
	<?php $i = 0; ?>
	<?php foreach ($list_users as $v) { ?>

		<?php $i ++; ?>
		<tr class="<?=$i % 2 ? 'row1' : 'row2'?>">
		
			<td><?php echo $v[0]; ?></td>
			<td style="text-align: center;">&euro;  <?php echo $v[1]; ?></td>
			
			<td style="text-align: center;">
				<input type="text" name="comment_<?=$v[2]?>" style="width: 200px;" />
			</td>
			
			<td style="text-align: right;">
				<input type="text" name="<?=$v[2]?>" style="width: 50px;" />
			</td>
		</tr>

	<?php } ?>

	<?php if ($formAction) { ?>

		<tr>
			<td colspan="4" style="text-align: right;"><button type="submit"><?php echo $submit; ?></button></td>
		</tr>
	
		</table>
		</form>
		
	<?php } else { ?>

		</table>

	<?php } ?>

<?php } else { ?>

	<p><?php echo $noUsers; ?></p>

<?php } ?>
