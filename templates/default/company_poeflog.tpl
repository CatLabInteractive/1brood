<h2><?=$poeflog?></h2>
<p><a href="<?=$return_url?>"><?=$return?></a></p>

<?php if (isset ($list_logs)) { ?>
	<table class="data" style="width: 100%;">
	
		<tr>
			<th><?=$datum?></th>
			<th><?=$amount?></th>
			<th><?=$balance?></th>
			<th><?=$actor?></th>
		</tr>
	
		<?php foreach ($list_logs as $log) { ?>
		<tr>
			<td style="width: 33%; text-align: center;"><?=$log['date']?></td>
			
			<td style="text-align: center; width: 17%;">
			
				<?php if ($log['amount'] >= 0) { ?>
					+ &euro; <?php echo Core_Tools::convert_price (abs ($log['amount'])); ?>
				<?php } else { ?>
					<span style="color: red; ">
						- &euro; <?php echo Core_Tools::convert_price (abs ($log['amount'])); ?>
					</span>
				<?php } ?>
				
			</td>
			
			<td style="text-align: center; width: 17%;">
			
				<?php if ($log['newpoef'] >= 0) { ?>
					<span style="color: #888888;">
						+ &euro; <?php echo Core_Tools::convert_price (abs ($log['newpoef'])); ?>
					</span>
				<?php } else { ?>
					<span style="color: #ff8888; ">
						- &euro; <?php echo Core_Tools::convert_price (abs ($log['newpoef'])); ?>
					</span>
				<?php } ?>
				
			</td>
			
			<td style="text-align: center;"><a href="<?=$log['actor_url']?>"><?=$log['actor_name']?></a></td>
		</tr>
		<?php } ?>
	
	</table>
<?php } else { ?>

	<p><?=$nologs?></p>

<?php } ?>