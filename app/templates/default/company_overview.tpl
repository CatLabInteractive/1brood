<?php if (isset ($admin_edit_link)) { ?>
	<p class="rightLink">
		<a href="<?php echo $admin_edit_link; ?>"><?php echo $admin_edit; ?></a>
	</p>
<?php } ?>

<h2><?php echo $title; ?></h2>

<?php if (isset ($notFound)) { ?>

	<p><?php echo $notFound; ?></p>

<?php } else { ?>

	<table class="data">

		<tr>
			<td class="links"><?php echo $naam; ?>:</td>
			<td class="rechts"><?php echo $c_name; ?></td>
		</tr>

		<tr>
			<td class="links"><?php echo $adres; ?>:</td>
			<td class="rechts"><?php echo $c_adres; ?></td>
		</tr>
		
		<tr>
			<td class="links">&nbsp;</td>
			<td class="rechts"><?php echo $c_postcode . ' ' . $c_gemeente; ?></td>
		</tr>
		
		<tr>
			<td class="links"><?php echo $users; ?>:</td>
			<td class="rechts"><?php echo $userAmount; ?></td>
		</tr>
		
		<tr>
			<td class="links"><?php echo $shops; ?>:</td>
			<td class="rechts"><?php echo $shopAmount; ?></td>
		</tr>
		
		<?php if (isset ($poeftotal_value)) { ?>
			<tr>
				<td class="links"><?php echo $poeftotal; ?>:</td>
				<td class="rechts"><?php echo $poeftotal_value; ?></td>
			</tr>
		<?php } ?>
	
	</table>
	
	<p class="rightLink">
	<?php if (isset ($admin_poefboek_link)) { ?>
		<a href="<?php echo $admin_poefboek_link; ?>"><?php echo $admin_poefboek; ?></a><?php if (isset ($admin_user_link)) { echo ', '; } ?>
	<?php } ?>
	
	<?php if (isset ($admin_user_link)) { ?>
			<a href="<?php echo $admin_user_link; ?>"><?php echo $admin_user; ?></a>
	<?php } ?>
	</p>
	
	<h2><?php echo $listusers; ?></h2>
	
	<?php if (isset ($list_users)) { ?>
	
		<table class="data">
		<?php foreach ($list_users as $v) { ?>
			<tr>
				<td style="width: 35%;">
					<a href="<?=$v[3]?>"><?php echo $v[0]; ?></a>
				</td>
				<td style="text-align: center; width: 25%;">
					<?php if ($v[2] >= 0) { ?>
						&euro; <?php echo $v[2]; ?>
					<?php } else { ?>
						<span style="color: red; font-weight: bold;">
							- &euro; <?php echo Core_Tools::convert_price (abs ($v[2])); ?>
						</span>
					<?php } ?>
				</td>
				<td style="text-align: center;"><?php echo $v[1]; ?></td>
			</tr>
		<?php } ?>
		</table>
	
	<?php } else { ?>
	
		<p><?php echo $noUsers; ?></p>
	
	<?php } ?>
	
	<?php if (isset ($admin_shops_link)) { ?>
		<p class="rightLink">
			<a href="<?php echo $admin_shops_link; ?>"><?php echo $admin_shops; ?></a>
		</p>
	<?php } ?>
	
	<h2><?php echo $listshops; ?></h2>
	<?php if (isset ($list_shops)) { ?>
	
		<table class="data">
		<?php foreach ($list_shops as $v) { ?>
			<tr>
				<td style="width: 75%;">

					<a href="<?php echo $v[2]; ?>"><?php echo $v[0]; ?></a>

				</td>

				<?php if (!empty ($v[1])) { ?>
					<td style="text-align: center;"><a href="<?php echo $v[1]; ?>"><?php echo $moderate; ?></a></td>
				<?php } else { ?>
					<td>&nbsp;</td>
				<?php } ?>
			</tr>
		<?php } ?>
		</table>
	
	<?php } else { ?>
	
		<p><?php echo $noShops; ?></p>
	
	<?php } ?>

<?php } ?>
