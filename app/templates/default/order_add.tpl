<h2><?php echo $title; ?></h2>


<?php if (isset ($done)) { ?>

	<p><?php echo $done; ?></p>
	<p><a href="<?php echo $back_url; ?>"><?php echo $back; ?></a></p>

<?php } else { ?>

	<p><a href="<?php echo $back_url; ?>"><?php echo $back; ?></a></p>

	<p><?php echo $about; ?></p>

	<form method="post" action="<?php echo $formAction; ?>">

		<p class="product">
			<span class="product"><?php echo $product; ?></span><br />
			<span class="details"><?php echo $details; ?></span>
		</p>
		
		<?php if (isset ($list_prices)) { ?>
			<ul style="list-style-type: none; padding-left: 0px;">
				<?php foreach ($list_prices as $v) { ?>
					<li>
						<input type="radio" name="price" id="price_<?=$v['id']?>" value="<?=$v['id']?>" <?php if ($v['checked']) { ?>checked="checked"<?php } ?> /> 
						<label for="price_<?=$v['id']?>" style="display: inline; margin: 0px;"><?=$v['name']?> (&euro; <?=$v['price']?>)</label>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
		
		<label>
			Aantal: <br />
			<input type="text" value="1" style="width: 30px;" name="amount" />
		</label>

		<label>
			Opmerkingen: <br />
			<input type="text" name="message" />
		</label>

		<button type="submit" name="submit" value="submit">Bestel</button>

	</form>

<?php } ?>
