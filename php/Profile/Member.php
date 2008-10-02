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

	private $id, $data = null, $isFound = false;

	public function __construct ($id)
	{
		$this->id = $id;
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

	public function getUsername ()
	{
		$this->loadData ();
		return $this->data['firstname'];
	}
	
	public function getFullname ()
	{
		$this->loadData ();
		return $this->data['realname'];
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

			if (count ($chk) == 0)
			{
				return 1;
			}
			else
			{
				return 2;
			}
		}
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
}
?>