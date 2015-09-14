<h2><?php echo $title; ?></h2>


<?php if (isset ($noPermission)) { ?>

	<p><?php echo $noPermission; ?></p>
	
<?php } else { ?>

	<?php if (isset($about)) { ?>
		<p><?php echo $about; ?></p>
	<?php } ?>

	<?php if (isset ($list_shops)) { ?>
		<table class="data">
			<?php foreach ($list_shops as $v) { ?>

				<tr>
					<td style="width: 75%;"><a href="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></a></td>
					<td style="text-align: center;">
					
						<a href="<?php echo $v[2]; ?>" onclick="return confirm ('<?php echo $conRem; ?>');"><?php echo $remove; ?></a>

					</td>
				</tr>

			<?php } ?>
		</table>
	<?php } else { ?>

		<p><?php echo $noShops; ?></p>

	<?php } ?>

	<h2><?php echo $addShopTitle; ?></h2>

	<?php if (isset ($list_addshop)) { ?>
		<form style="margin-bottom: 15px;" method="post" action="<?php $formAction; ?>">
		
			<label>
				<span><?php echo $selectShop; ?>:</span>
				<select name="add">
					<?php foreach ($list_addshop as $v) { ?>
						<option value="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></option>
					<?php } ?>
				</select>
			</label>

			<button><?php echo $submitAdd; ?></button>
		</form>
	<?php } ?>
	
	<p>
		<?php echo $addShop[0]; ?><a href="<?php echo $addShop_url; ?>" onclick="return confirm ('<?php echo $conToAdd; ?>');"><?php echo $addShop[1]; ?></a><?php echo $addShop[2]; ?>
	</p>

<?php } ?>
