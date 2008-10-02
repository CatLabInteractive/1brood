<h2><?php echo $title; ?></h2>
<p><?php echo $about; ?>

<h2><?php echo $companies; ?></h2>

<?php if (isset ($list_companies)) { ?>

	<p><?php echo $compAbout; ?></p>
	<ul style="margin-bottom: 15px;">
		<?php foreach ($list_companies as $v) { ?>
			<li>
			
				<div style="float: right; margin-right: 300px;">
					<a href="<?=$v[2]?>"><?=$poeflog?></a>
				</div>
			
				<a href="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></a>
			</li>
		<?php } ?>
	</ul>

<?php } else { ?>

	<p><?php echo $noCompanies; ?></p>

<?php } ?>

<p>
	<?php echo $addCompany[0]; ?><a href="<?php echo $addCompanyUrl; ?>"><?php echo $addCompany[1]; ?></a><?php echo $addCompany[2]; ?>
</p>

<?php if (isset ($list_pending)) { ?>

	<h2><?php echo $pending; ?></h2>
	<p><?php echo $aboutPending; ?></p>
	<ul>
		<?php foreach ($list_pending as $v) { ?>
			<li>
				<?php echo $v[0]; ?>
			</li>
		<?php } ?>
	</ul>

<?php } ?>