<?php
class Profile_Shop
{

	/*
		Return one shop instance
	*/
	public function getShop ($id)
	{
		static $in;

		$id = (int)$id;
		
		if (!isset ($in[$id]))
		{
			$in[$id] = new Profile_Shop ($id);
		}
		return $in[$id];
	}

	/*
		Return all shops
	*/
	public static function getShops ()
	{
		$db = Core_Database::__getInstance ();

		$l = $db->select
		(
			'shops',
			array ('*')
		);

		$o = array ();
		foreach ($l as $v)
		{
			$i = count ($o);
			$o[$i] = self::getShop ($v['s_id']);
			$o[$i]->setData ($v);
		}
		
		return $o;
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

	public function isFound ()
	{
		$this->loadData ();
		return $this->isFound;
	}

	private function loadData ()
	{
		if ($this->data === null)
		{
			$db = Core_Database::__getInstance ();
			$l = $db->select
			(
				'shops',
				array ('*'),
				"s_id = '".$db->escape ($this->getId ())."'"
			);

			if (count ($l) == 1)
			{
				$this->setData ($l[0]);
			}
			else
			{
				$this->isFound = false;
			}
		}
	}

	public function refreshData ()
	{
		$this->data = null;
	}

	public function getName ($toonGemeente = false)
	{
		$this->loadData ();

		$name = $this->data['s_name'];

		if ($toonGemeente && !empty ($this->data['s_gemeente']))
		{
			$name .= ' ('.$this->data['s_gemeente'].')';
		}
		
		return $name;
	}

	public function canModerate ($objMember)
	{
		if ($objMember)
		{
			$db = Core_Database::__getInstance ();
	
			$chk = $db->select
			(
				'players_shop',
				array ('plid'),
				"plid = '".$objMember->getId ()."' AND s_id = '".$this->getId ()."'"
			);
	
			return count ($chk) == 1;
		}
		else
		{
			return false;
		}
	}

	public function getMessage ()
	{
		$this->loadData ();
		return $this->data['s_message'];
	}
	
	public function getCategories ($addEmtpyFrame = false)
	{
		$db = Core_Database::__getInstance ();
		
		$cats = $db->select
		(
			'categories',
			array ('*'),
			"s_id = '".$this->getId ()."'"
		);
		
		$categories = array ();
		foreach ($cats as $v)
		{
			$categories[$v['c_id']] = $v;
			
			// Fetch the prices
			$categories[$v['c_id']]['prices'] = array ();
			
			$prices = $db->select
			(
				'categories_prices',
				array ('*'),
				"c_id = '".$v['c_id']."'"
			);
			
			foreach ($prices as $price)
			{
				$categories[$v['c_id']]['prices'][$price['p_id']] = $price;
			}
			
			if (count ($categories[$v['c_id']]['prices']) == 0)
			{
				$categories[$v['c_id']]['prices'] = array
				(
					array
					(
						'c_id' => 0,
						'p_id' => 0,
						'c_name' => 'Regular'
					)
				);
			}
		}
		
		if ($addEmtpyFrame)
		{
			$categories[0] = array
			(
				'c_id' => 0,
				'c_name' => 'General',
				'prices' => array
				(
					array
					(
						'c_id' => 0,
						'p_id' => 0,
						'c_name' => 'Regular'
					)
				)
			);
		}
		
		return $categories;
	}
	
	public function getMaxPricesPerCategory ($categories = null)
	{
		if (empty ($categories))
		{
			$categories = $this->getCategories (true);
		}
		
		$out = 0;
		foreach ($categories as $v)
		{
			if (count ($v['prices']) > $out)
			{
				$out = count ($v['prices']);
			}
		}
		
		return $out;
	}

	public function getProducts ($id = null)
	{
		$db = Core_Database::__getInstance ();
		
		// Get the categories
		$categories = $this->getCategories ();

		if ($id === null)
		{
			$prods = $db->select
			(
				'products',
				array ('*'),
				"s_id = '".$this->getId ()."'",
				"c_id ASC, p_name ASC"
			);
		}
		else
		{
			$prods = $db->select
			(
				'products',
				array ('*'),
				"s_id = '".$this->getId ()."' AND p_id = '".intval($id)."'",
				"c_id ASC, p_name ASC"
			);		
		}
		
		$products = array ();
		$i = 0;
		
		$maxPrices = $this->getMaxPricesPerCategory ($categories);
				
		foreach ($prods as $product)
		{
			$products[$i] = $product;
			$prices = explode (',', $product['p_price']);
			
			$products[$i]['prices'] = array ();
			
			if (isset ($categories[$product['c_id']]))
			{
				foreach ($categories[$product['c_id']]['prices'] as $v)
				{
					if (!isset ($prices[$v['p_id']]))
					{
						$products[$i]['prices'][$v['p_id']] = number_format (0, 2);
					}
					else
					{
						$products[$i]['prices'][$v['p_id']] = number_format($prices[$v['p_id']], 2);
					}
				}
			}
			else
			{
				$products[$i]['prices'][0] = number_format($prices[0], 2);
			}
			
			// Fill the whitespaces
			for ($ip = 0; $ip < $maxPrices; $ip ++)
			{
				if (!isset ($products[$i]['prices'][$ip]))
				{
					$products[$i]['prices'][$ip] = null;
				}
			}
			
			$i ++;
		}

		return $products;
	}

	public function hasProduct ($id)
	{
		$db = Core_Database::__getInstance ();

		$id = (int)$id;
		
		$prods = $db->select
		(
			'products',
			array ('*'),
			"s_id = '".$this->getId ()."' AND p_id = '".$id."'",
			"p_name ASC"
		);

		return count ($prods) == 1;
	}

	public function getProduct ($id)
	{
		$product = $this->getProducts ($id);
		return count ($product) == 1 ? $product[0] : false;
	}

	public function getCurrency ()
	{
		$this->loadData ();
		return $this->data['s_currency'];
	}
	
	public function getLocation ()
	{
		$this->loadData ();
		
		return $this->data['s_gemeente'];
	}
	
	public function getModerators ()
	{
		$db = Core_Database::__getInstance ();
		
		$users = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					players.*
				FROM
					players_shop
				LEFT JOIN
					players USING(plid)
				WHERE
					players_shop.s_id = {$this->getId()}
			")
		);
		
		$out = array ();
		foreach ($users as $v)
		{
			$p = Profile_Member::getMember ($v['plid']);
			$p->setData ($v);
			$out[] = $p;
		}

		return $out;
	}
	
	/*
		Moderators
	*/
	public function addModerator ($objUser)
	{
		$db = Core_Database::__getInstance ();
		$db->insert
		(
			'players_shop',
			array
			(
				'plid' => $objUser->getId (),
				's_id' => $this->getId ()
			)
		);
	}
	
	public function removeModerator ($objUser)
	{
		$db = Core_Database::__getInstance ();
		$db->customQuery
		("
			REMOVE FROM
				players_shop
			WHERE
				plid = {$objUser->getId ()} AND
				s_id = {$this->id()}
		");
	}

	public function getAdress ()
	{
		$this->loadData ();

		return Core_Tools::output_varchar ($this->data['s_name']) . '<br />' .
			(!empty ($this->data['s_adres']) ? Core_Tools::output_varchar ($this->data['s_adres']) . '<br />' : null) .
			(!empty ($this->data['s_postcode']) ? Core_Tools::output_varchar ($this->data['s_postcode']) . ' ' : null) .
			(!empty ($this->data['s_gemeente']) ? Core_Tools::output_varchar ($this->data['s_gemeente']) : null);
	}
}
?>
