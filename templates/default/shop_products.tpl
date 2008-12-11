<h2><?=$title?></h2>

<?php if (!empty ($message)) { ?>
	<div class="message">
		<?php echo $message; ?>
	</div>
<?php } ?>

<?php $this->setTextSection ('products', 'order'); ?>

<?php foreach ($list_categories as $category) {  ?>
	<?php if (count ($category['products']) > 0) { ?>
		<h3><?php echo $category['name']; ?></h3>
		<?php if (count ($category['products']) > 0) { ?>
			<table class="productsInfo">
		
				<?php $last = count ($category['products']); $i = 0; foreach ($category['products'] as $v) { $i ++;?>
				
					<?php if ($i % 15 == 1) { ?>
						<tr>
							<th style="text-align: left;">Product</th>
							<?php foreach ($category['prices'] as $price) { ?>
								<th><?php echo Core_Tools::output_varchar ($price['c_name']); ?></th>
							<?php } ?>
						</tr>
					<?php } ?>
				
					<tr class="prodMain">
				
						<td><?php echo $v[0]; ?></td>
					
						<?php foreach ($category['prices'] as $price) { ?>
							<td style="width: 10%;" class="price"><?php echo $currency . ' ' . $v[2][$price['p_id']]; ?></td>
						<?php } ?>
					
					</tr>
	
					<?php if ($i == $last) { ?>
						<tr class="prodDetail last">
					<?php } else { ?>
						<tr class="prodDetail">
					<?php } ?>
				
						<td><?php echo $v[1]; ?></td>
	
						<?php foreach ($category['prices'] as $price) { ?>
							<td>&nbsp;</td>
						<?php } ?>
					</tr>
				
				
				<?php } ?>
			</table>
		<?php } else { ?>
			<p><?php echo $noProducts; ?></p>
		<?php } ?>
	<?php } ?>
<?php } ?>
