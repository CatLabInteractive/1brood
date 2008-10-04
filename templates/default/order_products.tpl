<h2><?php echo $title; ?></h2>

<?php if (intval ($poefboek_value) >= 0) { ?>
	<p class="true" style="text-align: center;"><?=$poefboek?></p>
<?php } else { ?>
	<p class="false" style="text-align: center;"><?=$poefboek?></p>
<?php } ?>

<?php if (!empty ($message)) { ?>
	<div class="message">
		<?php echo $message; ?>
	</div>
<?php } ?>

<?php foreach ($list_categories as $category) {  ?>
	<?php if (count ($category['products']) > 0) { ?>
		<h3><?php echo $category['name']; ?></h3>
		<?php if (count ($category['products']) > 0) { ?>
			<table class="productsInfo">
		
				<?php $last = count ($category['products']); $i = 0; foreach ($category['products'] as $v) { $i ++;?>
				
					<?php if ($i % 15 == 1) { ?>
						<tr>
							<th style="text-align: left; padding-left: 2px;">Product</th>
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
							<td style="text-align: center;">
								<a href="<?php echo $v[3][$price['p_id']]; ?>"><?php echo $order; ?></a>
							</td>
						<?php } ?>
					</tr>
				
				
				<?php } ?>
			</table>
		<?php } else { ?>
			<p><?php echo $noProducts; ?></p>
		<?php } ?>
	<?php } ?>
<?php } ?>
