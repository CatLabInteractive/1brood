<h2>Bestelling #<?=$orderId?></h2>
<p><?php echo $date->format ('d/m/Y'); ?></p>

<?php if (isset ($sended)) { ?>

	<p class="true" id="orderIsSent"><?=$sended?></p>

<?php } ?>

<table class="data">
	<tr>
		<td style="vertical-align: top; width: 50%;">
			<h3><?=$company?></h3>
			<?=$company_adres?>
		</td>

		<td style="vertical-align: top; width: 50%;">
			<h3><?=$shop?></h3>
			<?=$shop_adres?>
		</td>
	</tr>
</table>

<div id="order_order">

	<h3><?=$listProducts?></h3>

	<?php if (isset ($list_products)) { $i = 0; $last = count ($list_products); ?>
	
		<table class="productsInfo">

			<?php foreach ($list_products as $v) { $i ++; ?>

				<tr class="prodMain <?php if (empty ($v[3])) { ?> noProdDetail<?php if ($i == $last) { ?> lastRow<?php } } ?>">
				
					<td class="productName">
						<strong><?=$v[0]?></strong> <?=$v[4]?>
					</td>
					
					<?php if (!empty ($v[1])) { ?>
						<td style="text-align: center; color: gray; width: 34%" class="prodOrderName">
							<?php echo $v[1]; ?>&nbsp;
						</td>
					<?php } ?>
					
					<td style="width: 33%; text-align: center;"><?php echo $currency . ' ' . $v[2]; ?></td>
				</tr>
				
				<?php if (!empty ($v[3])) { ?>
					<?php if ($i == $last) { ?>
						<tr class="prodDetail lastRow">
					<?php } else { ?>
						<tr class="prodDetail">
					<?php } ?>
						<td colspan="<?=$table_cols?>">
							<?=$v[3]?>
						</td>
					</tr>
				<?php } ?>
			<?php $i ++; } ?>
				

		</table>
		
	<?php } ?>

	<?php if (isset ($mail_action)) { ?>
		<div class="noPrint">
			<h3><?=$sendMail?></h3>

			<form method="post" action="<?=$mail_action?>">

				<label>

					<?=$email?>:<br />
					<input type="text" value="" name="email" />

				</label>

				<button type="submit"><?=$sendIt?></button>
				<button type="reset" onclick="print(); return false;"><?=$printIt?></button>

			</form>
		</div>
	<?php } ?>

	<p class="noScreen">
		<?=$thanks?>
	</p>

</div>
