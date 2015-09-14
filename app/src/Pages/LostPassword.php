<?php
class Pages_LostPassword extends Pages_Page
{
	private $sPage;
	
	public function __construct ($page)
	{
		$this->sPage = $page;
	}
	
	protected function getContent ()
	{
		$id = Core_Tools::getInput ('_GET', 'id', 'int');
		$key = Core_Tools::getInput ('_GET', 'key', 'varchar');
		
		if ($id && $key)
		{
			return $this->getChangePassword ($id, $key);
		}
		else
		{
			return $this->getForm ();
		}
	}
	
	private function getForm ()
	{
		$page = new Core_Template ();
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('main');
		$text->setSection ('lostpass');
		
		// Fetch e-mail
		$email = Core_Tools::getInput ('_POST', 'email', 'email');
		
		if ($email)
		{
			if ($this->sendNewPassword ($email))
			{
				$page->set ('done', $text->get ('done'));
			}
			else
			{
				$page->set ('error', $text->get ('error'));
			}
		}
		
		$page->set ('lostpass', $text->get ('lostpass'));
		$page->set ('about', $text->get ('about'));
		$page->set ('email', $text->get ('email'));
		$page->set ('submit', $text->get ('submit'));
		
		return $page->parse ('lostPassword.tpl');
	}
	
	private function getSecKey ()
	{
		$key = "";
		
		for ($i = 0; $i < 8; $i ++)
		{
			$int = mt_rand (1, 29);
			$key .= $int < 10 ? $int : chr (mt_rand (97, 121));
		}
		
		return $key;
	}
	
	private function sendNewPassword ($email)
	{
		// Search for email
		$db = Core_Database::__getInstance ();
		
		$user = $db->select
		(
			'players',
			array ('plid', 'realname', 'email'),
			"email = '".$db->escape ($email)."'"
		);
		
		if (count ($user) != 1)
		{
			return false;
		}
		
		// Update the secret key
		$key = $this->getSecKey ();
		
		// User ID
		$user = $user[0];
		
		// Update key
		$db->update
		(
			'players',
			array
			(
				'seckey' => $key
			),
			"plid = ".intval ($user['plid'])
		);
		
		// Send the mail
		$this->sendMail (intval ($user['plid']), $key, $user['email'], $user['realname']);
		
		return true;
	}
	
	private function sendMail ($id, $key, $email, $toName = "")
	{
		$text = Core_Text::__getInstance ();
	
		$myself = Profile_Member::getMyself ();
	
		$mail = new Mailer_PHPMailer ();

		$mail->IsHTML(false);
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
		
		$mail->FromName = 'noreply';

		$mail->addAddress ($email, $toName);

		$mail->Subject = $text->get ('subject', 'lostpass', 'main');
		$mail->Body = Core_Tools::putIntoText 
		(
			$text->get ('text1', 'lostpass', 'main') . "\n\n" . 
			$text->get ('text2', 'lostpass', 'main') . "\n\n" . 
			$text->get ('text3', 'lostpass', 'main') . "\n\n" . 
			$text->get ('text4', 'lostpass', 'main'),
			array
			(
				'key' => $key,
				'name' => $toName,
				'url' => self::getUrl ('page=lostPassword&id='.$id.'&key='.$key, true)
			)
		);
		
		$mail->Priority = 2;
		$mail->Send ();
	}
	
	private function getChangePassword ($id, $key)
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('main');
		$text->setSection ('lostpass');
	
		// Check the key
		$db = Core_Database::__getInstance ();
		
		$chk = $db->select
		(
			'players',
			array ('plid'),
			"plid = ".intval ($id)." AND seckey = '".$db->escape ($key)."'"
		);
		
		if (trim ($chk) != "" AND count ($chk) === 1)
		{
			$password1 = Core_Tools::getInput ('_POST', 'pass1', 'varchar');
			$password2 = Core_Tools::getInput ('_POST', 'pass2', 'varchar');
			
			$page = new Core_Template ();
			
			$page->set ('title', $text->get ('changepass'));
			$page->set ('about', $text->get ('aboutchange'));
			$page->set ('pass1', $text->get ('pass1'));
			$page->set ('pass2', $text->get ('pass2'));
			$page->set ('submit', $text->get ('submitchange'));
			
			if ($password1 && $password1 === $password2)
			{
				$login = Core_Login::__getInstance ();
				$login->setPassword ($chk[0]['plid'], $password1);
				
				$page->set ('success', $text->get ('passchanged'));
			}
			elseif ($password1 || $password2)
			{
				$page->set ('warning', $text->get ('passmismatch'));
			}
			
			return $page->parse ('lostPassword_res.tpl');
		}
		else
		{
			return '<p class="false">Security Key Not Found.</p>';
		}
	}
}
?>
