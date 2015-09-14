<h2><?=$title?></h2>

<form method="post" action="<?=$action?>">
	<h3>Moderators</h3>
	
	<?php if (isset ($list_moderators)) { ?>	
		<ul>
			<?php foreach ($list_moderators as $v) { ?>
				<li>
					<a href="<?=$v['url']?>"><?=$v['name']?></a> (<a href="<?=$v['removeUrl']?>">verwijder</a>)
				</li>
			<?php } ?>
		</ul>
	<?php } ?>
	
	<p>Voeg moderators toe door het email adres van de nieuwe moderator toe te voegen.</p>
	
	<?php if (isset ($error)) { ?>
		<p class="false"><?=$error?></p>
	<?php } ?>
	
	<label>
		<span>E-mail adres:</span>
		<input type="text" name="moderator_mail" />
	</label>
	
	<button type="submit">Moderator toevoegen</button>
</form>
