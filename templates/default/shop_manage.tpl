<h2><?php echo $title; ?></h2>

<select style="display: none;" id="default_product_categories">
	<option value="0">-</option>
	<?php if (isset ($list_categories)) { ?>
		<?php foreach ($list_categories as $vv) { ?>
			<option value="<?=$vv['id']?>"><?=$vv['name']?></option>								
		<?php } ?>
	<?php } ?>
</select>

<form action="<?php echo $formAction; ?>" method="post">
	<label>

		<?php echo $message; ?>:<br />

		<textarea name="shopMessage"><?php echo $message_value; ?></textarea>

	</label>

	<p style="float: right; margin-top: 8px;">
		<a href="<?=$editCategory_url?>"><?=$editCategory?></a>
	</p>

	<h3><?php echo $products; ?></h3>

	<table id="products">

		<tr>
			<th style="width: 15%;" class="prodName"><?php echo $productName; ?></th>
			<th style="width: 40%;" class="prodText"><?php echo $productText; ?></th>
			
			<?php for ($count = 0; $count < $priceColsToShow; $count ++) { ?>
				<th class="prodPrice"><?php echo $productPrice; ?> <?=chr($count+65)?></th>
			<?php } ?>
			
			<th style="width: 15%;" class="prodPrice"><?php echo $category; ?></th>
		</tr>

		<?php if (isset ($list_products)) { ?>

			<?php $i = 1; foreach ($list_products as $v) { ?>
			<tr>
				<td>
					<input type="hidden" name="productOrg<?php echo $i; ?>" value="<?php echo $v[0]; ?>" />
					<input type="text" class="prodName" name="productName<?php echo $i; ?>" value="<?php echo $v[1]; ?>" />
				</td>
				
				<td>
					<input type="text" class="prodText" name="productText<?php echo $i; ?>" value="<?php echo $v[2]; ?>" />
				</td>
				
				<?php for ($count = 0; $count < $priceColsToShow; $count ++) { ?>
				<td>
					<input type="text" class="prodPrice" name="productPrice<?php echo $i; ?>_<?=$count?>" value="<?php echo $v[3][$count]; ?>" />
				</td>
				<?php } ?>

				<td>
					<select name="categoryId<?php echo $i; ?>">
						<option value="0">-</option>
						<?php if (isset ($list_categories)) { ?>
							<?php foreach ($list_categories as $vv) { ?>
								<?php if ($v[4] == $vv['id']) { ?>
									<option value="<?=$vv['id']?>" selected="selected"><?=$vv['name']?></option>
								<?php } else { ?>
									<option value="<?=$vv['id']?>"><?=$vv['name']?></option>								
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</select>
				</td>
			</tr>

			<?php $i ++; } ?>

		<?php } else { ?>

			<tr id="noRows">
				<td colspan="<?php echo $priceColsToShow + 3 ?>"><?php echo $noRows; ?></td>
			</tr>

		<?php } ?>

	</table>

	<!--
	<p>
		<?php echo $toAddRow[0]; ?><a href="javascript:void(0);" onclick="addProductRow();"><?php echo $toAddRow[1]; ?></a><?php echo $toAddRow[2]; ?>
	</p>
	-->

	<div style="float: none;">
		<button type="button" onfocus="addProductRow(<?=$priceColsToShow?>);"><?php echo $addRow; ?></button>
		<button type="submit" name="submit" value="saveSettings"><?php echo $submit; ?></button>
	</div>
</form>
