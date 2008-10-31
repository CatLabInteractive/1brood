<?php

class Core_Tools
{

	/*
	
		Translate a mysql date - field to a unix timestamp.
		Returns false if date is not set.
	
	*/
	public static function dateToTimestamp ($date)
	{
	
		return $date != '0000-00-00' ? 
			mktime (
			
				0,
				0,
				1,
				substr ($date, 5, 2),
				substr ($date, 8, 2),
				substr ($date, 0, 4)
			 
		): false;
	
	}
	
	public static function dateToMysql ($day, $month, $year)
	{
	
		return self::addZeros ($year, 4).'-'.self::addZeros ($month, 2).'-'.self::addZeros ($day, 2);
	
	}
	
	public static function datetimeToMysql ($day, $month, $year, $hour, $minute, $seconds)
	{
	
		return self::addZeros ($year, 4).
			'-'.self::addZeros ($month, 2).
			'-'.self::addZeros ($day, 2)
			.' '.self::addZeros ($hour, 2)
			.':'.self::addZeros ($minute, 2).
			':'.self::addZeros ($seconds, 2);
	
	}
	
	public static function timestampToMysql ($time = null)
	{
	
		if ($time == null)
		{
		
			$time = time ();
		
		}
	
		return self::dateToMysql (date ('d', $time), date ('m', $time), date ('Y', $time));
	
	}
	
	public static function timestampToMysqlDatetime ($time = null)
	{
	
		if ($time == null)
		{
		
			$time = time ();
		
		}
		
		return self::datetimeToMysql (date ('d', $time), date ('m', $time), date ('Y', $time), 
			date ('H', $time), date ('i', $time), date ('s', $time));
	
	}

	public function getArrayFirstValue ($a)
	{
		foreach ($a as $k => $v)
			return array ($k, $v);
	}
	
	public static function addZeros ($int, $totaal)
	{
	
		while (strlen ($int) < $totaal)
		{
		
			$int = "0".$int;
		
		}
		
		return $int;
	
	}

	public static function getInput ($dat, $key, $type, $default = false)
	{

		global $$dat;
		$dat = $$dat;

		if (!isset ($dat[$key])) {

			return $default;
		}

		else {
			$value = $dat[$key];
			
			// Check for some small "input filtering"
			switch ($type)
			{
				case 'float':

					$value = str_replace (',', '.', $value);
			}
		
			// Check if the value has the right type
			if (Core_Tools::checkInput ($value, $type))
			{
				return $value;
			}

			else
			{
				return $default;
			}
		}
	}

	public static function checkInput ($value, $type)
	{
		if ($type == 'bool' || $type == 'text')
		{
			return true;
		}
		
		elseif ($type == 'varchar')
		{
			return true;
		}
		
		elseif ($type == 'password')
		{
			return strlen ($value) > 2;
		}
		
		elseif ($type == 'email')
		{
			return strlen ($value) > 2;
		}
		
		elseif ($type == 'username')
		{
			return preg_match ('/^[a-zA-Z0-9]{3,20}$/', $value);
		}
		
		elseif ($type == 'md5')
		{
			return strlen ($value) == 32;
		}
		
		elseif ($type == 'int')
		{
			return is_numeric ($value);
		}

		elseif ($type == 'float')
		{
			return is_numeric ($value);
		}
		
		else 
		{
			return false;
			echo 'fout: '.$type;
		}
	}
	
	public static function convert_price ($basic_price)
	{
	
		$basic_price = str_replace (",", ".", $basic_price);
		$basic_price = number_format ($basic_price, 2, ".", "");
		
		return $basic_price;
	
	}

	public static function putIntoText ($text, $ar = array(), $delimiter = '@@') 
	{
		foreach ($ar as $k => $v) 
		{
			$text = str_replace ($delimiter.$k, $v, $text);
		}
		return $text;
	}

	public static function output_title ($title)
	{

		return htmlentities (stripslashes($title), ENT_QUOTES, 'UTF-8');

	}
	
	public function date_long ($stamp)
	{
	
		$text = Core_Text::__getInstance ();
		
		$dag = $text->get ('day'.(date ('w', $stamp) + 1), 'days', 'main');
		$maand = $text->get ('mon'.date ('m', $stamp), 'months', 'main');
	
		return Core_Tools::putIntoText (
			$text->get ('longDateFormat', 'dateFormat', 'main'),
			array
			(
				$dag,
				date ('d', $stamp),
				$maand,
				date ('Y', $stamp)
			)
		);
	
	}
	
	public static function splitLongWords ($input)
	{
	
		$array = explode (' ', $input);
		
		foreach ($array as $k => $v)
		{
		
			$array[$k] = wordwrap ($v, 20, ' ', 1);
		
		}
		
		return implode (' ', $array);
	
	}
	
	public static function output_text ($convert, $p = true)
	{

		//$input = Core_Tools::splitLongWords ($input);

		/* Config: breaks: */
		$p_open = '<p>';
		$p_close = '</p>';
		$p_break = '<br  />';

		$convert = stripslashes ($convert);
		
		$convert = htmlentities ($convert, ENT_QUOTES, 'UTF-8');
		
		// Basic layout
		$convert = preg_replace ( "/\[b](.*?)\[\/b]/si", '<strong>\\2</strong>', $convert );
		$convert = preg_replace ( "/\[u](.*?)\[\/u]/si", '<u>\\2</u>', $convert );
		$convert = preg_replace ( "/\[i](.*?)\[\/i]/si", '<i>\\2</i>', $convert );
		$convert = preg_replace ( "/\[lt](.*?)\[\/lt]/si", '<span style="text-decoration: line-trough;">\\2</span>', $convert );
		
		
		if (!$p) {
			// Headers
			$convert = preg_replace (
				"/\[h(.*?)](.*?)\[\/h(.*?)]/si",
				'<h\\1>\\2</h\\1>',
				$convert
			);
		}
		
		else {
			// Headers
			$convert = preg_replace (
			
				"/\[h(.*?)](.*?)\[\/h(.*?)]/si",
				$p_close.'<h\\1>\\2</h\\1>'.$p_open,
				$convert
				
			);
		}

		// Hyperlinks
		$convert = eregi_replace(
			"\[url]([^\[]*)\[/url]",
			"<a target=\"_BLANK\" href=\"\\1\">\\1</a>", $convert);
		
		$convert = eregi_replace(
			"\[url=([^\[]*)\]([^\[]*)\[/url]",
			"<a target=\"_BLANK\" href=\"\\1\">\\2</a>", $convert);

		/*
		// Images align=left
		$convert = eregi_replace(
			"\[img]([-_./a-zA-Z0-9!&%#?,'=:~]+)\[/img]",
			"<img class=\"tc\" style=\"margin: 0px 5px;\" src=\"\\1\">", $convert);
		
		// Images align=left
		$convert = eregi_replace(
			"\[img:([-_./a-zA-Z0-9!&%#?,'=:~]+)\]([-_./a-zA-Z0-9!&%#?,'=:~]+)\[/img]",
			"<img class=\"tc\" style=\"margin: 0px 5px;\"  align=\"\\1\" src=\"\\2\">", $convert);
		

		
		// fonts
		$convert = eregi_replace(
			"\[color=([a-zA-Z0-9]+)\]",
			"<font class=\"tc\" color=\"\\1\">", $convert);

		// quote met titel
		$convert = preg_replace("/\[quote: (.*?)]/si", 
			$p_close."<blockquote class=\"tc\" ><h3>Quote"
			." \\1:</h3><p>", $convert);

		// quote zonder titel
		$convert = str_replace("[quote]", $p_close."<blockquote>".$p_open, $convert);

		$convert = str_replace("[/quote]", $p_close."</blockquote>".$p_open, $convert);
		*/

		// Paragraphs and line breaks
		$convert = str_replace ("\r", "", $convert);
		$convert = str_replace ("\n\n", $p_close.$p_open, $convert);
		$convert = str_replace ("\n", $p_break, $convert);
		
		if ($p) {
			$convert = $p_open . $convert . $p_close;
		}
		
		// Remove "empty p"
		$convert = str_replace ($p_open.$p_break, $p_open, $convert);
		$convert = str_replace ($p_open.$p_close, '', $convert);
		
		return $convert;
	
	}
	
	public static function output_form ($text)
	{
	
		return htmlentities (stripslashes ($text) , ENT_QUOTES, 'UTF-8');
	
	}
	
	public static function output_varchar ($text)
	{
	
		$input = Core_Tools::splitLongWords ($text);
		return htmlentities (stripslashes ($text), ENT_QUOTES, 'UTF-8');
	
	}

	public static function pages ($template, $total, $url, $perPage, $eindeUrl = null)
	{

		global $_GET;
		global $text;

		if (!isset ($_GET['curp'])) {

			$_GET['curp'] = 1;

		}

		// config
		$maxAantalSnelclicks = 14;
		$curp = $_GET['curp'];

		// calculate how much + which records should be shown
		$totPages = ceil ($total / $perPage);

		if ($totPages == 0) {

			$totPages = 1;

		}

		$start = ($curp - 1) * $perPage;

		$newSql = "limit ".$start.", ".$perPage;

		if ($curp < ($totPages)) {

			$next = $url."&curp=".($curp + 1).$eindeUrl;
			$nextT = $text->get ("nextPage", "general");

			$template->addCondition ("pages.next", true);
			$template->addContent ("pages.next.url", $next);
			$template->addContent ("pages.next.name", $nextT);

		}

		else {

			$template->addCondition ("pages.next", false);

		}

		if ($curp > 1) {

			$prev = $url."&curp=".($curp - 1).$eindeUrl;
			$prevT = $text->get ("prevPage", "general");

			$template->addCondition ("pages.previous", true);
			$template->addContent ("pages.previous.url", $prev);
			$template->addContent ("pages.previous.name", $prevT);

		}

		else {

			$template->addCondition ("pages.previous", false);

		}

		$deHelft = round($maxAantalSnelclicks / 2);

		if ($curp < $deHelft) {

			$snelcount = 1;
			$morevar = $deHelft - $curp + 1;

		} 

		else {

			$snelcount = $curp - $deHelft;
			$morevar = $deHelft;

		}
		
		if ($curp > ($totPages - $deHelft) && $curp > $deHelft) {

			$snelvar = $totPages - $curp;
			$snelcount = $snelcount - ($morevar - $snelvar);

		}
		
		if ($snelcount < 1) { 

			$snelcount = 1; 

		}

		$snelmax = $snelcount + $maxAantalSnelclicks + 1;

		// replace the stuff
		$pS = $snelcount;
		$pE = min ($snelmax, $totPages);

		for ($p = $pS; $p <= $pE; $p ++) {

			if ($p < 10) {

				$k = "0".$p;

			}

			else {

				$k = $p;

			}

			if ($curp <> $p) {

				$template->addListValue ("pages.list", 
					array ($url."&curp=".$p.$eindeUrl, $k));

			}

			else {

				$template->addListValue ("pages.list", 
					array ($url."&curp=".$p.$eindeUrl, 
					'<font class="selectedPage"><u>'.$k.'</u></font>'));

			}

		}

		return (($newSql));

	}
	
	public static function color_mkwebsafe ( $in )
	{
		// put values into an easy-to-use array
		$vals['r'] = hexdec( substr($in, 0, 2) );
		$vals['g'] = hexdec( substr($in, 2, 2) );
		$vals['b'] = hexdec( substr($in, 4, 2) );
		
		// loop through
		foreach( $vals as $val )
		{
		// convert value
		$val = ( round($val/51) * 51 );
		// convert to HEX
		$out .= str_pad(dechex($val), 2, '0', STR_PAD_LEFT);
		}
		
		return $out;
	}
	
	public static function splitPages 
		(
		$db, 
		$table, 
		$data, 
		$where, 
		$order, 
		$count_col, 
		$page, 
		$url_l, 
		$limit = 50, 
		$ajax = true,
		$maxAantalSnelclicks = 15
		)
	{
		
		$maxAantalSnelclicks -= 2;
	
		/* Current page */
		$current = System_Tools::getInput ('_GET', 'page', 'int', 0);
		
		/* Count the pages */
		$l = $db->select ($table, array ("count($count_col) as aantal"), $where);
		$aantal = $l[0]['aantal'];

		$limit = max ($limit, 1);
		$pages = ceil ($aantal / $limit);
	
		$deHelft = round ($maxAantalSnelclicks / 2);

		if ($current < $deHelft) {

			$snelcount = 1;
			$morevar = $deHelft - $current + 1;

		} 

		else {

			$snelcount = $current - $deHelft;
			$morevar = $deHelft;

		}

		if ($current > ($pages - $deHelft) && $current > $deHelft) {

			$snelvar = $pages - $current;
			$snelcount = $snelcount - ($morevar - $snelvar);

		}
		
		if ($snelcount < 1) { 

			$snelcount = 1; 

		}

		$snelmax = $snelcount + $maxAantalSnelclicks + 1;

		// replace the stuff
		$pS = $snelcount - 1;
		$pE = min ($snelmax, $pages);
		
		for ($i = $pS; $i < $pE; $i ++)
		{
		
			$url = Modules_Module::getUrl ($url_l.'&page='.$i);
			if ($ajax)
			{
			
				$page->addListValue ('pages', array ($i+1, $url[0], $url[1], $current == $i));
			
			}
			
			else {
			
				$page->addListValue ('pages', array ($i+1, $url[0], null, $current == $i));
			
			}
		
		}
		
		/* Previous Link */
		if ($current > 0) {
		
			$url = Modules_Module::getUrl ($url_l.'&page='.($current-1));
			$page->setVariable ('pages_prev', $url);
		
		}
		
		else {
		
			$page->setVariable ('pages_prev', false);
		
		}

		/* Next Link */
		if ($current < ($pages - 1)) {
		
			$url = Modules_Module::getUrl ($url_l.'&page='.($current+1));
			$page->setVariable ('pages_next', $url);
		
		}
		
		else {
		
			$page->setVariable ('pages_next', false);
		
		}
		
		return $db->select ($table, $data, $where, $order, ($current * $limit).', '.$limit);
	
	}
	
	public static function getConfirmLink ()
	{
	
		return 'confirmed';
	
	}
	
	public static function checkConfirmLink ($link)
	{
	
		return ($link == self::getConfirmLink ());
	
	}
	
	public static function getCountdown ($future, $class = 'counter')
	{
		$timeLeft = $future - time ();
		
		$hours = floor ($timeLeft / 3600);
		$minutes = floor (($timeLeft - $hours * 3600) / 60);
		$seconds = $timeLeft - $hours * 3600 - $minutes * 60;
		
		if ($hours < 10) $hours = '0'.$hours;
		if ($minutes < 10) $minutes = '0'.$minutes;
		if ($seconds < 10) $seconds = '0'.$seconds;
	
		return '<span class="'.$class.'">'.$hours.':'.$minutes.':'.$seconds.'</span>';
	}

	public static function getDuration ($duration)
	{
		$hours = floor ($duration / 3600);
		$minutes = floor ( ($duration - $hours * 3600) / 60 );
		$seconds = floor ( $duration - $hours * 3600 - $minutes * 60 );

		if ($hours < 10) { $hours = '0' . $hours; }
		if ($minutes < 10) { $minutes = '0' . $minutes; }
		if ($seconds < 10) { $seconds = '0' . $seconds; }

		if ($hours > 0)
		{
			$dur = $hours . ':' . $minutes . ':' . $seconds;
		}

		else
		{
			$dur = $minutes . ':' . $seconds;
		}

		return $dur;
	}
	
	public static function sendMail ($subject, $html, $email, $toName = "", $fromName = null, $fromEmail = null)
	{
		$mail = new Mailer_PHPMailer ();

		$mail->IsHTML(true);
		$mail->CharSet = 'UTF8';
		
		$useAuth = defined ('MAILER_USER') && defined ('MAILER_PASSWORD');

		// Authenticate
		$mail->Mailer = MAILER_MAILER;
		$mail->SMTPAuth = $useAuth;
		
		$mail->Host = MAILER_HOST;
		$mail->Port = MAILER_PORT;
		
		if ($useAuth)
		{
			$mail->Username = MAILER_USER;
			$mail->Password = MAILER_PASSWORD;
		}

		// Make yourself.
		if (defined ('MAILER_FROM'))
		{
			$mail->From = MAILER_FROM;
		}
		
		if (isset ($fromName))
		{
			$mail->FromName = $fromName;
		}
		
		if (isset ($fromEmail))
		{
			$mail->AddReplyTo ($fromEmail);
		}
		
		//$mail->ConfirmReadingTo = $myself->getEmail ();

		$mail->addAddress ($email, $toName);
		
		if (isset ($fromName) && isset ($fromEmail))
		{
			$mail->addCC ($fromEmail, $fromName);
		}

		$mail->Subject = $subject;
		$mail->Body = $html;
		
		$mail->Priority = 2;

		$mail->Send ();
	}

}

?>
