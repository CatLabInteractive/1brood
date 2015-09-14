<?php
class Profile_Member
{
	public static function getMyself ()
	{
		$login = Core_Login::__getInstance ();
		if ($login->isLogin ())
		{
			return self::getMember ($login->getUserId ());
		}
		else
		{
			return false;
		}
	}

	public static function getMember ($id)
	{
		static $in;

		if (!isset ($in[$id]))
		{
			$in[$id] = new Profile_Member ($id);
		}

		return $in[$id];
	}
	
	public static function getFromEmail ($email)
	{
		$db = Core_Database::__getInstance ();
		
		$data = $db->select
		(
			'players',
			array ('plid'),
			"email = '{$db->escape ($email)}'"
		);
		
		if (count ($data) == 1)
		{
			return self::getMember ($data[0]['plid']);
		}
		else
		{
			return false;
		}
	}

	private $id, $data = null, $isFound = false;

	public function __construct ($id)
	{
		$this->id = $id;
	}

	public function getId ()
	{
		return intval ($this->id);
	}
	
	public function setData ($data)
	{
		$this->data = $data;
		$this->isFound = true;
	}
	
	private function loadData ()
	{
		if ($this->data === null)
		{
			// Select
			$db = Core_Database::__getInstance ();
			
			$l = $db->select
			(
				'players',
				array ('*'),
				"players.plid = '".$this->id."'"
			);
			
			if (count ($l) == 1)
			{
				$this->setData ($l[0]);
			}
			else
			{
				$this->data = false;
			}
		}
	}
	
	/*
		Reset the local cache & reload the data (if needed)
	*/
	public function reloadData ()
	{
		$this->data = null;
	}

	public function getUsername ()
	{
		$this->loadData ();
		return $this->data['realname'];
	}
	
	public function getFullname ()
	{
		return $this->getFirstName () . ' ' . $this->getName ();
	}
	
	public function getFirstName ()
	{
		$this->loadData ();
		return $this->data['firstname'];	
	}
	
	public function getName ()
	{
		$this->loadData ();
		return $this->data['lastname'];	
	}

	public function getEmail ()
	{
		$this->loadData ();
		return $this->data['email'];
	}

	/*
		Reg statusses:
		0 = not logged in yet
		1 = account created, no companie
	*/
	public function getRegStatus ()
	{
		$this->loadData ();
	
		$login = Core_Login::__getInstance ();
		$db = Core_Database::__getInstance ();

		if (!$login->isLogin ())
		{
			return 0;
		}
		else
		{
			// logged in
			$chk = $db->select
			(
				'players_comp',
				array ('c_id'),
				"plid = '".$this->id."'"
			);

			if (count ($chk) == 0 && intval ($this->data['noCompany']) != 1)
			{
				return 1;
			}
			else
			{
				return 2;
			}
		}
	}
	
	public function setNoCompany ()
	{
		$db = Core_Database::__getInstance ();
		
		$db->customQuery
		("
			UPDATE
				players
			SET
				noCompany = 1
			WHERE
				plid = {$this->getId()}
		");
		
		$this->reloadData ();
	}

	public function getMyCompanies ($pending = false)
	{
		$db = Core_Database::__getInstance ();
	
		// logged in
		$chk = $db->getDataFromQuery ($db->customQuery
		("
			SELECT
				c.*,
				p.compStatus
			FROM
				players_comp p
			LEFT JOIN
				companies c ON p.c_id = c.c_id
			WHERE
				p.plid = '".$this->getId ()."'
				AND ".( $pending ? "p.isApproved = '0'" : "p.isApproved = '1'")."
		"));

		$companies = array ();
		foreach ($chk as $v)
		{
			$i = count ($companies);
			$companies[$i] = Profile_Company::getCompany ($v['c_id']);
			$companies[$i]->setData ($v);
		}

		return $companies;
	}
	
	public function getMyShops ()
	{
		$db = Core_Database::__getInstance ();
	
		// logged in
		$chk = $db->getDataFromQuery ($db->customQuery
		("
			SELECT
				shops.*
			FROM
				players_shop
			LEFT JOIN
				shops ON players_shop.s_id = shops.s_id
			WHERE
				players_shop.plid = {$this->getId()}
		"));

		$companies = array ();
		foreach ($chk as $v)
		{
			$i = count ($companies);
			$companies[$i] = Profile_Shop::getShop ($v['s_id']);
			$companies[$i]->setData ($v);
		}

		return $companies;
	}

	public function isFound ()
	{
		$this->loadData ();
		return $this->isFound;
	}

	public function getPendingOrders ()
	{
		$db = Core_Database::__getInstance ();

		$l = $db->getDataFromQuery ($db->customQuery
		("
			SELECT
				*
			FROM
				orders o
			LEFT JOIN
				order_prods op ON o.o_id = op.o_id
			WHERE
				op.plid = '".((int)$this->getId ())."'
				AND o.o_isDone = '0'
		"));

		$o = array ();
		foreach ($l as $v)
		{
			$shop = Profile_Shop::getShop ($v['s_id']);
			$company = Profile_Company::getCompany ($v['c_id']);

			$o[] = array
			(
				$shop->getProduct ($v['p_id']),
				$v,
				$company,
				$shop
			);
		}

		return $o;
	}

	public function removeProductFromBasket ($id)
	{
		// First: let's find the product
		$db = Core_Database::__getInstance ();

		$l = $db->select
		(
			'order_prods',
			array ('*'),
			"op_id = '".((int)$id)."' AND plid = '".$this->getId ()."'"
		);

		if (count ($l) == 1)
		{
			// Now find if unavtive
			$o = $db->select
			(
				'orders',
				array ('o_id'),
				"o_id = '".$l[0]['o_id']."' AND o_isDone = '0'"
			);

			if (count ($o) == 1)
			{
				$db->remove
				(
					'order_prods',
					"op_id = '".((int)$id)."' AND plid = '".$this->getId ()."'"
				);
			}
		}
	}
	
	public function sendReminder ($company)
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('reminder');
	
		$email = $this->getEmail ();
		
		// Get company owner
		$owner = $company->getOwner ();
		
		// Check for negative poefboek
		$poefboek = $company->getMemberPoefboek ($this);
		
		if ($poefboek > 0)
		{
			$txt = Core_Tools::output_text ($text->getFile ('mails/reminder'));
		}
		else
		{
			$txt = Core_Tools::output_text 
			(
				Core_Tools::putIntoText
				(
					$text->getFile ('mails/angry_reminder'),
					array 
					(
						'poefboek' => $poefboek,
						'admin' => Core_Tools::output_varchar ($owner->getFullname ())
					)
				)	
			);
		}
		
		if ($owner)
		{
			Core_Tools::sendMail 
			(
				$text->get ('subject'), 
				$txt,
				$email, 
				$this->getFullname (), 
				$owner->getFullname (), 
				$owner->getEmail (),
				false
			);
		}
		
		// Notify members by message:
		$db = Core_Database::__getInstance ();
		
		$accounts = $db->select
		(
			'im_users',
			array ('im_user'),
			"im_player = ".$this->getId ()." AND im_activated = 1"
		);
		
		$url = 'https://www.imified.com/api/bot/';
		
		foreach ($accounts as $v)
		{
			$data = array
			(
			    'botkey' => '53881418-A97D-9713-415C46EA2843C806',
			    'apimethod' => 'send',
			    'userkey' => $v['im_user'],     // char
			    'msg' => 'Tijd voor broodjes! Ga snel naar http://www.1brood.be/ !',
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERPWD, 'daedelson@gmail.com:aukv0006');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
			$xml = curl_exec ($ch);
			
			if (!$xml)
			{
				echo curl_error ($ch);
			}
			
			curl_close($ch);
		}
	}
}
?>
