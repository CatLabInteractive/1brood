<?=$order_overview?>

<form method="post" style="margin-top: 10px;" action="<?=$action_url?>"
	onSubmit="return confirm ('<?php echo addslashes ($confirmSubmit);?>');" >

	<input type="hidden" name="confirmKey" value="<?=$confirmKey?>" />

	<button type="submit"><?=$sendOrder?></button>

</form>