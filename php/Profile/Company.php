<?php
class Profile_Company
{

	public static function getCompany ($id)
	{
		static $in;

		$id = (int)$id;

		if (!isset ($in[$id]))
		{
			$in[$id] = new Profile_Company ($id);
		}
		
		return $in[$id];
	}

	private $id, $data = null, $isFound;

	public function __construct ($id)
	{
		$this->id = $id;
	}

	public static function getCompanies ()
	{
		$db = Core_Database::__getInstance ();

		$l = $db->select
		(
			'companies',
			array ('*')
		);

		$o = array ();
		foreach ($l as $v)
		{
			$i = count ($o);
			$o[$i] = Profile_Company::getCompany ($v['c_id']);
			$o[$i]->setData ($v);
		}

		return $o;
	}

	public function getId ()
	{
		return $this->id;
	}

	public function setData ($data)
	{
		$this->data = $data;
		$this->isFound = true;
	}
	
	public function getData ()
	{
		$this->loadData ();
		return $this->data;
	}

	private function loadData ()
	{
		if ($this->data === null)
		{
			$db = Core_Database::__getInstance ();
			
			$com = $db->select
			(
				'companies',
				array ('*'),
				"c_id = '".$db->escape ($this->id)."'"
			);
			
			if (count ($com) == 1)
			{
				$this->setData ($com[0]);
			}
			else
			{
				$this->data = false;
				$this->isFound = false;
			}
		}
	}

	public function getName ($toonGemeente = false)
	{
		$this->loadData ();

		$mname = $this->data['c_name'];

		if ($toonGemeente && !empty ($this->data['c_gemeente']))
		{
			$mname .= ' ('.$this->data['c_gemeente'].')';
		}
		
		return $mname;
	}
	
	public function isFound ()
	{
		$this->loadData ();
		return $this->isFound;
	}
	
	public function getUsers ()
	{
		$db = Core_Database::__getInstance ();
		
		$rows = $db->select
		(
			'players_comp',
			array ('*'),
			"c_id = '".$db->escape ($this->id)."'",
			"compStatus DESC"
		);
		
		$o = array ();
		foreach ($rows as $v)
		{
			if ($v['isApproved'] == 0)
			{
				$v['compStatus'] = -1;
			}
		
			$o[] = array
			(
				Profile_Member::getMember ($v['plid']),
				$this->getCompStatusTranslation ($v['compStatus']),
				$v['poefboek']
			);
		}
		return $o;
	}
	
	public function getCompStatusTranslation ($id)
	{
		switch ($id)
		{
			case -1:
				return 'pending';
			break;
			
			case 0:
				return 'user';
			break;
			
			case 1:
				return 'moderator';
			break;
			
			case 2:
				return 'administrator';
			break;
			
			default:
				return 'user';
			break;
		}
	}

	public function getCompStatusFromTranslation ($trans)
	{
		switch ($trans)
		{
			case 'user':
				return '0';
			break;

			case 'moderator':
				return '1';
			break;

			case 'administrator':
				return '2';
			break;
		}
	}
	
	public function getUserStatus ($objUser)
	{
		$db = Core_Database::__getInstance ();
		
		$l = $db->select
		(
			'players_comp',
			array ('compStatus'),
			"plid = '".$objUser->getId ()."' && c_id = '".$this->getId ()."'"
		);
		
		if (count ($l) == 1)
		{
			return $this->getCompStatusTranslation ($l[0]['compStatus']);
		}
		else
		{
			return $this->getCompStatusTranslation (-1);
		}
	}
	
	public function getShops ()
	{
		$db = Core_Database::__getInstance ();

		$l = $db->select
		(
			'companies_shop',
			array ('*'),
			"c_id = '".$db->escape ($this->getId ())."'"
		);

		$o = array ();
		foreach ($l as $v)
		{
			$o[] = Profile_Shop::getShop ($v['s_id']);
		}

		return $o;
	}

	public function hasShop ($objShop)
	{
		$db = Core_Database::__getInstance ();

		$l = $db->select
		(
			'companies_shop',
			array ('*'),
			"c_id = '".$this->getId ()."' AND s_id = '".$objShop->getId ()."'"
		);

		return count ($l) == 1;
	}

	public function removeShop ($remove)
	{
		$db = Core_Database::__getInstance ();
		
		$db->remove
		(
			'companies_shop',
			"c_id = '".$db->escape ($this->getId ())."' AND s_id = '".$db->escape ($remove)."' "
		);
	}

	public function addShop ($sid)
	{
		$db = Core_Database::__getInstance ();

		// Check for duplicate
		$chk = $db->select
		(
			'companies_shop',
			array ('s_id'),
			"c_id = '".$db->escape ($this->getId ())."' AND s_id = '".$db->escape ($sid)."'"
		);

		if (count ($chk) == 0)
		{
			$db->insert
			(
				'companies_shop',
				array
				(
					'c_id' => $this->getId (),
					's_id' => $sid
				)
			);
		}
	}

	public function setUserStatus ($member, $status)
	{
		$myself = Profile_Member::getMyself ();
		$db = Core_Database::__getInstance ();

		if ($myself && $myself->getId () != $member->getId ())
		{
			switch ($status)
			{

				case 'user':
				case 'moderator':
				
					// Regular update
					$db->update
					(
						'players_comp',
						array
						(
							'compStatus' => $this->getCompStatusFromTranslation ($status),
							'isApproved' => 1
						),
						"plid = '".$member->getId ()."' AND c_id = '".$this->getId ()."' "
					);
					
				break;
				
				case 'administrator':
				
					// This is something special: you lose the right.
					$db->update
					(
						'players_comp',
						array
						(
							'compStatus' => $this->getCompStatusFromTranslation ($status),
							'isApproved' => 1
						),
						"plid = '".$member->getId ()."' AND c_id = '".$this->getId ()."' "
					);

					$db->update
					(
						'players_comp',
						array ( 'compStatus' => $this->getCompStatusFromTranslation ('moderator') ),
						"plid = '".$myself->getId ()."' AND c_id = '".$this->getId ()."' "
					);
				
				break;
				
				case 'remove':
					$db->remove
					(
						'players_comp',
						"plid = '".$member->getId ()."' AND c_id = '".$this->getId ()."' "
					);
				break;
				
				case 'pending':
				
					$db->update
					(
						'players_comp',
						array
						(
							'compStatus' => 0,
							'isApproved' => 0
						),
						"plid = '".$member->getId ()."' AND c_id = '".$this->getId ()."' "
					);
				
				break;
			}
		}
	}

	public function addMember ($objMember, $autoAccept = false)
	{
		$db = Core_Database::__getInstance ();

		// Check for existance
		$l = $db->select
		(
			'players_comp',
			array ('plid'),
			"plid = '".$objMember->getId ()."' AND c_id = '".$this->getId ()."' "
		);

		if (count ($l) == 0)
		{
			$db->insert
			(
				'players_comp',
				array
				(
					'plid' => $objMember->getId (),
					'c_id' => $this->getId (),
					'isApproved' => ($autoAccept ? '1' : '0'),
					'compStatus' => '-1'
				)
			);
		}
	}

	public function getAdress ()
	{
		$this->loadData ();

		return Core_Tools::output_varchar ($this->data['c_name']) . '<br />' .
			Core_Tools::output_varchar ($this->data['c_adres']) . '<br />' .
			Core_Tools::output_varchar ($this->data['c_postcode']) . ' ' .
			Core_Tools::output_varchar ($this->data['c_gemeente']);
	}

	public function getMemberPoefboek ($objUser)
	{
		$db = Core_Database::__getInstance ();
		
		$l = $db->select
		(
			'players_comp',
			array ('poefboek'),
			"plid = '".$objUser->getId ()."' && c_id = '".$this->getId ()."'"
		);
		
		if (count ($l) == 1)
		{
			return $l[0]['poefboek'];
		}
		else
		{
			return false;
		}
	}
	
	public function getPoefboekTotal ()
	{
		$poef = 0;
		foreach ($this->getUsers () as $v)
		{
			$poef += $this->getMemberPoefboek ($v[0]);
		}
		return $poef;
	}
	
	public function addToMemberPoefboek ($objUser, $amount, $order = 'moderator')
	{
		$db = Core_Database::__getInstance ();
		
		$db->update
		(
			'players_comp',
			array
			(
				'poefboek' => '++' . $amount
			),
			"plid = '".$objUser->getId ()."' && c_id = '".$this->getId ()."'"
		);
		
		$newAmount = $db->select
		(
			'players_comp',
			array ('poefboek'),
			"plid = '".$objUser->getId ()."' && c_id = '".$this->getId ()."'"
		);
		
		$iNewAmount = 0;
		if (count ($newAmount) == 1)
		{
			$iNewAmount = $newAmount[0]['poefboek'];
		}
		
		$this->addPoefboekLog ($objUser, $amount, $iNewAmount, $order);
	}

	public function takeFromMemberPoefboek ($objUser, $amount, $order = 'order', $orderId = false)
	{
		$db = Core_Database::__getInstance ();
		
		$db->update
		(
			'players_comp',
			array
			(
				'poefboek' => '--' . $amount
			),
			"plid = '".$objUser->getId ()."' && c_id = '".$this->getId ()."'"
		);
		
		$newAmount = $db->select
		(
			'players_comp',
			array ('poefboek'),
			"plid = '".$objUser->getId ()."' && c_id = '".$this->getId ()."'"
		);
		
		$iNewAmount = 0;
		if (count ($newAmount) == 1)
		{
			$iNewAmount = $newAmount[0]['poefboek'];
		}
		
		
		$this->addPoefboekLog ($objUser, $amount * -1, $iNewAmount, $order, $orderId);
	}
	
	private function addPoefboekLog ($objUser, $amount, $newAmount, $action, $actor = false)
	{
		if (!$actor)
		{
			$login = Core_Login::__getInstance ();
			$actor = $login->getUserId ();
		}
		
		$db = Core_Database::__getInstance ();
		
		// Insert log
		$db->insert
		(
			'players_poefboeklog',
			array
			(
				'plid' => $objUser->getId (),
				'c_id' => $this->getId (),
				'l_amount' => $amount,
				'l_newpoef' => $newAmount,
				'l_date' => 'NOW()',
				'l_action' => $action,
				'l_actor' => $actor
			)
		);
	}
	
	/*
							'date' => $v['date'],
						'amount' => Core_Tools::convert_price ($v['amount']),
						'actor_name' => $v['actor_name'],
						'actur_url' => $v['actor_url']
	*/
	public function getPoefboekLog ($objUser)
	{
		$db = Core_Database::__getInstance ();
		
		$logs = $db->select
		(
			'players_poefboeklog',
			array ('*', 'UNIX_TIMESTAMP(l_date) AS date'),
			"c_id = '".$this->getId ()."' AND plid = '".$objUser->getId ()."'",
			'l_date DESC'
		);
		
		$out = array ();
		
		$text = Core_Text::__getInstance ();
		
		foreach ($logs as $v)
		{
			switch ($v['l_action'])
			{
				case 'order':
					$actor_name = $text->get ('order', 'poeflog', 'company') . ' #'.$v['l_actor'];
					$actor_url = Pages_Page::getUrl ('page=order&oid='.$v['l_actor']);
				break;
				
				default:
					$user = Profile_Member::getMember ($v['l_actor']);
					if ($user->isFound ())
					{
						$actor_name = $user->getUsername ();
						$actor_url = 'mailto:'.$user->getEmail ();
					}
					else
					{
						$actor_name = 'user_not_found';
						$actor_url = '#';
					}
				break;
			}
			
			$out[] = array
			(
				'date' => $v['date'],
				'amount' => $v['l_amount'],
				'actor_name' => $actor_name,
				'actor_url' => $actor_url,
				'newpoef' => $v['l_newpoef']
			);
		}
		
		return $out;
	}
}
?>