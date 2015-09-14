<h2><?php echo $title; ?></h2>
<p><?=$about?></p>

<h3><?=$cats?></h3>

<?php if (isset ($list_cats)) { ?>
<form method="post">
	<fieldset>
		<ul>
			<?php foreach ($list_cats as $v) { ?>
				<li>
					<strong><?=$v['name']?></strong> 
					<a href="<?=$v['remUrl']?>" onclick="return confirm ('<?=$youSure?>');"><?=$remove?></a><br />
					<?=$prices?>:<br />
					<ul>
						<?php $i = 0; foreach ($v['prices'] as $vv) { ?>
							<li><input name="price_<?=$v['id']?>_<?=$i?>" type="text" value="<?=$vv['name']?>" /></li>
						<?php $i ++;  } ?>
						
						<li><input title="<?=$newPrice?>" type="text" name="price_<?=$v['id']?>_<?=($i+1)?>" /></li>
					</ul>
				</li>
			<?php } ?>
		</ul>
	
		<button type="submit"><?=$savePrices?></button>
	</fieldset>
</form>
	
<?php } else { ?>
	<p><?=$nocats?></p>
<?php } ?>

<h3><?=$addcat?></h3>
<form action="<?=$addcat_url?>" method="post">
	<fieldset>

		<legend><?=$addcat?></legend>
		
		<label for="catname"><?=$catname?>:</label>			
		<input type="text" name="catname" id="catname" />
	
		<button type="submit"><?=$addsubmit?></button>
		
	</fieldset>
</form>
