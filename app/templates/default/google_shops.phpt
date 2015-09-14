<?php $this->setTextSection ('shops', 'order'); ?>

<h2><?=$this->getText ('title')?></h2>
<p><?=$this->getText ('about')?></p>

<?php if (isset ($list_shops)) { ?>
	<ul>
		<?php foreach ($list_shops as $v) { ?>
			<li>
				<a href="<?=$v['url']?>"><?=$v['name']?></a> <?php if (!empty ($v['location'])) { ?>(<?=$v['location']?>)<?php } ?>
			</li>
		<?php } ?>
	</ul>
<?php } ?>
