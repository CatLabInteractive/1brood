<h2>Winkel toevoegen</h2>

<p>
	Is je bedrijf reeds in de database? Vraag dan aan de huidige beheerders om jou toe te voegen.<br />
	Hun contactgegeven kan je op de winkelpagina vinden.
</p>

<?php if (isset ($list_shops)) { ?>
	<p>
		Volgende winkels zitten reeds in onze databank:
	</p>

	<ul>
		<?php foreach ($list_shops as $v) { ?>
			<li><a href="<?=$v['url']?>"><?=$v['name']?></a> (<?=$v['location']?>)</li>
		<?php } ?>
	</ul>
<?php } ?>

<p>
	Klik <a href="<?=$addshop_url?>">hier</a> om een nieuwe winkel toe te voegen als hij niet in de lijst voorkomt.
</p>
