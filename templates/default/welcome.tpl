<h2>Welkom!</h2>

<p>Welkom bij 1Brood. Het eten is bijna klaar!</p>

<form method="post" action="<?=$action?>">
	<div>
		<label class="checkbox important">
			<input type="radio"  name="welcome_selection" checked="checked" value="honger" />
			<span>Ik heb honger</span>
		</label>
	
		<p>
			Ik ben werknemer of run mijn eigen bedrijf.<br />
			Elke dag bestel ik lunch bij een broodjes- of andere lunch zaak.<br />
			Ik wil 1Brood gebruiken om dit proces eenvoudiger te maken.
		</p>
	
		<label class="checkbox important">
			<input type="radio"  name="welcome_selection" value="geld" />
			<span>Ik verkoop lunch</span>
		</label>
	
		<p>
			Ik verkoop broodjes of andere lunch en doe elke dag mijn ronde.<br />
			Elke dag ontvang ik een tiental mailtjes met bestellingen.<br />
			Meestal zijn deze handgeschreven en praktisch onleesbaar.<br />
			Telkens ik een nieuwe prijslijst heb, moet ik die naar iedereen toesturen.<br />
			Ik wil 1Brood gebruiken om snellere en betere lunch te leveren.
		</p>
	
		<button type="submit">Volgende</button>
	</div>
</form>
