<?php
class Pages_API extends Pages_Page
{
	public function getHTML ()
	{
		$action = Core_Tools::getInput ('_GET', 'page', 'varchar');
		$action = explode ('/', $action);
		
		if (isset ($action[1]))
		{
			switch ($action[1])
			{
				case 'cron':
					return $this->doCron ();
				break;
			
				default:
				case 'im':
					return $this->getIMAPI ();
				break;
			}
		}
		else
		{
			echo 'API not specified.';
		}
	}
	
	private function getIMAPI ()
	{
		// Fetch the user key
		$key = Core_Tools::getInput ('_POST', 'userkey', 'varchar');
		
		if (!empty ($key))
		{
			$db = Core_Database::__getInstance ();
		
			$user = $db->select
			(
				'im_users',
				array ('im_player', 'im_activated'),
				"im_user = '".$db->escape ($key)."'"
			);
		
			if (count ($user) == 0)
			{
				// Request to login
				$this->processIMLogin ($key, Core_Tools::getInput ('_POST', 'msg', 'varchar'));
			}
			
			// Check for not activated
			elseif ($user[0]['im_activated'] == 0)
			{
				$this->processIMActivate ($key, Core_Tools::getInput ('_POST', 'msg', 'varchar'));
			}
			
			else
			{
				// User is authenticated
				$this->processIMCommand ($user[0]['im_player'], Core_Tools::getInput ('_POST', 'msg', 'varchar'));
			}
		}
		else
		{
			echo 'What are you doing here?';
		}
	}
	
	private function processIMLogin ($key, $msg)
	{
		$text = Core_Text::__getInstance ();
	
		$db = Core_Database::__getInstance ();
		
		$commands = explode (' ', $msg);
		$command = array_shift ($commands);
		
		switch ($command)
		{
			case 'login':

				if (count ($commands) > 1)
				{
					$email = array_shift ($commands);
					
					// Search in the database for this user
					$user = $db->select
					(
						'players',
						array ('plid', 'realname', 'email'),
						"email = '".$db->escape ($email)."'"
					);
					
					if (count ($user) == 1)
					{
						// Invent a new key
						$passkey = substr (md5 (rand (0, 100000)), 0, 6);
					
						$db->insert
						(
							'im_users',
							array
							(
								'im_user' => $key,
								'im_player' => $user[0]['plid'],
								'im_key' => $passkey
							)
						);
						
						// Send the secret key to this user
						Core_Tools::sendMail 
						(
							$text->get ('subject', 'bot', 'main'), 
							Core_Tools::output_text 
							(
								Core_Tools::putIntoText
								(
									$text->getFile ('mails/bot'),
									array
									(
										'name' => $user[0]['realname'],
										'key' => $passkey
									)
								)
							),
							$user[0]['email']
						);

						echo 'We have found your account. We have sent an email containing a secret key.<br />Please show me the key here.';
					}
					else
					{					
						echo 'This user ('.$username.') is not found. Please try again.';						
					}
				}
				else
				{
					echo '"Login" should be followed by your email and password.';
				}

			break;
			
			default:
				echo 'Please login in 1Brood by typing "Login <your email>".<br />';
				echo 'We will start sending out reminders as soon as you are logged in.';
			break;
		}
	}
	
	private function processIMActivate ($key, $msg)
	{
		$db = Core_Database::__getInstance ();
	
		// Check if message is right
		$chk = $db->update
		(
			'im_users',
			array
			(
				'im_activated' => 1
			),
			"im_user = '".$db->escape ($key)."' AND im_key = '".$db->escape ($msg)."'"
		);
		
		if ($chk == 1)
		{
			echo 'Your 1Brood account is now linked to this IM account.';
		}
		else
		{
			echo 'This is the wrong key! Come back when you find the right one.';
		}
	}
	
	private function processIMCommand ($plid, $msg)
	{
		$player = Profile_Member::getMember ($plid);
		if ($player->isFound ())
		{
			$commands = explode (' ', $msg);
			$command = array_shift ($commands);
			
			echo 'Hi there, '.$player->getUsername ().'. How are you doing?';
		}
		else
		{
			echo 'Something went wrong. Please contact the administrator.';
		}		
	}
	
	private function doCron ()
	{
		$db = Core_Database::__getInstance ();
	
		$hour = date ('H');
		$day = date ('w');
		
		if ($day > 0 && $day < 6)
		{		
			// Select all companies
			$companies = $db->select
			(
				'companies',
				array ('*'),
				"c_hour = ".intval ($hour)
			);
		
			echo '<pre>';
		
			echo 'It\'s '.$hour.'h, time for reminders!' . "\n";
		
			$count = 0;
			foreach ($companies as $v)
			{
				$company = Profile_Company::getCompany ($v['c_id']);
				echo 'Sending mails to '.$company->getName ().".\n";
			
				$company->sendReminders ();
				
				$count ++;
			}
			
			if ($count == 0)
			{
				echo 'No reminders were sent.';
			}
			
			echo '</pre>';
		}
		else
		{
			echo '<pre>It\'s weekend.</pre>';
		}
	}
}
?>
