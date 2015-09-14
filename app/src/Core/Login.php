<?php

class Core_Login
{

	private $level, $uid, $warning = false;

	/*
	
		Returns an Core_Login object.
		Only one / level allowed, so you always get the same object.
	
	*/
	static public function __getInstance ($level = 0)
	{
		static $in;
		if (!isset ($in[$level]))
		{
			$in[$level] = new Core_Login ($level);
		}
		return $in[$level];
	}
	
	public function __construct ($level)
	{	
		/* Store level */
		$this->level = $level;
		
		/* Check for login */
		$uid = Core_Tools::getInput ('_SESSION', 'plid', 'int', false);
		$logout = Core_Tools::getInput ('_GET', 'logout', 'bool', false);
		
		/* Check for logout */
		if ($logout)
		{
			$this->logout ();
		}

		/* Player has logged in */
		elseif ($uid)
		{
			$this->uid = $uid;
		}

		/* Player has not logged in: check for cookies */
		else 
		{
			// Check for different auth types
			$authType = Core_Tools::getInput ('_SESSION', 'loginAuthType', 'varchar');
			$authUID = Core_Tools::getInput ('_SESSION', 'loginAuthUID', 'int');
			$authKey = Core_Tools::getInput ('_SESSION', 'loginAuthSesKey', 'varchar');

			if (!empty ($authType) && $authUID > 0)
			{
				// There is an auth, just make sure there is a user in the database.
				$db = Core_Database::__getInstance ();

				$data = $db->select
				(
					'players',
					array ('plid, authSesKey'),
					"authType = '$authType' && authUID = '$authUID'"
				);

				if (count ($data) == 1)
				{
					$this->uid = $data[0]['plid'];
					
					if ($data[0]['authSesKey'] != $authKey)
					{
						// Update the session key!
						$db->update
						(
							'players',
							array
							(
								'authSesKey' => $authKey
							),
							"plid = '".$this->uid."'"
						);
					}
				}

				else
				{
					// Only one ring to rule them all.
					if (count ($data) > 0)
					{
						$db->remove ('players', "authType = '$authType' && authUID = '$authUID'");
					}
					
					$this->uid = $db->insert
					(
						'players',
						array
						(
							'authType' => $authType,
							'authUID' => $authUID,
							'authSesKey' => $authKey,
							'activated' => 1
						)
					);
				}
			}

			else
			{
				/* Check for cookies */
				$uid = Core_Tools::getInput ('_COOKIE', 'un'.$this->level, 'username', false);
				$pas = Core_Tools::getInput ('_COOKIE', 'ps'.$this->level, 'md5', false);
				$sal = Core_Tools::getInput ('_COOKIE', 'sl'.$this->level, 'md5', false);

				/* Process login (to be written) */
				$this->uid = false;
			}
		}
	}
	
	public function getUserId ()
	{
		return $this->uid;
	}
	
	public function isLogin ()
	{
		return $this->uid != false;
	}
	
	public function changePassword ($password, $newPassword)
	{
		$db = Core_Database::__getInstance ();
	
		if ($this->isLogin ())
		{
			$hash1 = md5 ($password);
			
			$user = $db->select
			(
				'players',
				array ('*'),
				"plid = '".$this->uid."' ".
				"AND password1 = md5(concat('there',password2,'and back".$hash1."again')) AND activated = '1'"
			);
			
			if (count ($user) == 1)
			{
				/* Make new password */
				$hash1 = md5 ($newPassword);
				$hash2 = $user[0]['password2'];
				$hash = md5 ('there'.$hash2.'and back'.$hash1.'again');
				
				$db->update
				(
					'players',
					array 
					(
						'password1' => $hash
					),
					"uid = '{$user[0]['uid']}'"
				);
			}
			
			else
			{
				$this->warning = 'oldpass_no_match';
				return false;
			}
		
		}
		
		else
		{
			return false;
		}
	
	}
	
	public function setPassword ($userId, $newPassword)
	{
		$db = Core_Database::__getInstance ();
		
		$user = $db->select
		(
			'players',
			array ('*'),
			"plid = ".intval ($userId)
		);
		
		if (count ($user) != 1)
		{
			return false;
		}
	
		/* Make new password */
		$hash1 = md5 ($newPassword);
		$hash2 = $user[0]['password2'];
		$hash = md5 ('there'.$hash2.'and back'.$hash1.'again');
		
		$db->update
		(
			'players',
			array 
			(
				'password1' => $hash,
				'seckey' => ''
			),
			"plid = '{$user[0]['plid']}'"
		);
		
		return true;
	}
	
	public static function checkLoginDetails ($email, $password)
	{
		$db = Core_Database::__getInstance ();
	
		$hash1 = md5 ($password);
		
		$user = $db->select
		(
			'players',
			array ('plid', 'realname', 'email'),
			"(email = '$email' OR realname = '$email') ".
			"AND password1 = md5(concat('there',password2,'and back".$hash1."again')) AND activated = '1'"
		);
		
		if (count ($user) == 1)
		{
			return $user[0];
		}
		
		else
		{
			return false;
		}
	}
	
	public function login ($username, $password)
	{
		global $_SESSION;
	
		/* Get database */
		$db = Core_Database::__getInstance ();
		
		/* Check for login */
		
		/*
		
			$hash1 = md5 ($password);
			$hash2 = md5 ('a hobbits tale'.date ('dmyhis').rand (0, 10000).'by Bilbo Baggings.');
			$hash = md5 ('there'.$hash2.'and back'.$hash1.'again');
		
		*/
		
		$user = self::checkLoginDetails ($username, $password);
		
		/* Login is accepted */
		if ($user)
		{
			$this->uid = $user['plid'];
			$this->name = $user['realname'];
			$_SESSION['plid'] = $this->uid;
			
			return true;
		}
		
		else {
			$this->warning = 'user_not_found';
			return false;			
		}
	}
	
	public function logout ()
	{
		global $_SESSION;
		$_SESSION['plid'] = false;
	}
	
	public function getName ()
	{
		return !empty ($this->name) ? $this->name : 'User not found.';
	}
	
	public function getWarnings ()
	{
		return $this->warning;
	}
	
	public function registerAccount ($user, $email, $password, $firstname, $lastname)
	{
		$db = Core_Database::__getInstance ();
		
		/* Hash the password */
		$hash1 = md5 ($password);
		$hash2 = md5 ('a hobbits tale'.date ('dmyhis').rand (0, 10000).'by Bilbo Baggings.');
		
		/* Make the hash */
		$hash = md5 ('there'.$hash2.'and back'.$hash1.'again');
		
		/* Add to the user database */
		$db->insert
		(
			'players', 
			array
			(
				'realname'	=>	$user,
				'email'		=>	$email,
				'password1'	=>	$hash,
				'password2'	=>	$hash2,
				'firstname'	=> 	$firstname,
				'lastname'	=>	$lastname,
				'activated'	=>	1
			)
		);

		// Now: login
		$this->login ($email, $password);
	}
}

?>
