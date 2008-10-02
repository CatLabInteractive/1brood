<h2><?php echo $title; ?></h2>

<?php if (isset ($notLoggedIn)) { ?>

	<p><?php echo $notLoggedIn; ?></p>
	
<?php } else { ?>

	<p><?php echo $about; ?></p>
	<?php if (isset ($list_companies)) { ?>

		<ul>
			<?php foreach ($list_companies as $company) { ?>

				<li>

					<?php echo $company[0]; ?>
					<ul>
					<?php foreach ($company[2] as $shop) { ?>
						<li>
							<a href="<?php echo $shop[2]; ?>">
								<?php echo $shop[0]; ?>
							</a>
						</li>
					<?php } ?>
					</ul>

				</li>

			<?php } ?>
		</ul>

	<?php } else { ?>

		<p style="font-weight: bold;"><?php echo $noCompanies; ?></p>

	<?php } ?>

	<?php echo $pendingOrders; ?>

<?php } ?>