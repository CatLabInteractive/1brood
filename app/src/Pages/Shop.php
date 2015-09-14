<?php
class Pages_Shop extends Pages_Page
{
	public function getContent ()
	{
		$action = Core_Tools::getInput ('_GET', 'action', 'varchar', false);
		$shop = Profile_Shop::getShop (Core_Tools::getInput ('_GET', 'id', 'int'));
		$myself = Profile_Member::getMyself ();

		if ($action == 'add')
		{
			return $this->getAddShop ();
		}
		elseif ($action == 'manage' && $shop->isFound () && $myself && $shop->canModerate ($myself))
		{
			return $this->getShopManagement ($shop);
		}
		elseif ($action == 'moderator' && $shop->isFound () && $myself && $shop->canModerate ($myself))
		{
			return $this->getModeratorManagement ($shop);
		}
		elseif ($action == 'categories' && $shop->isFound () && $myself && $shop->canModerate ($myself))
		{
			return $this->getManageCategories ($shop);
		}
		elseif ($shop->isFound ())
		{
			return $this->getOverview ($shop);
		}
		else
		{
			return '<p>Invalid input.</p>';
		}
	}

	public function getAddShop ()
	{
		$login = Core_Login::__getInstance ();
		$db = Core_Database::__getInstance ();
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('shop');
		$text->setSection ('addShop');

		if ($login->isLogin ())
		{
			$page = new Core_Template ();

			$page->set ('title', $text->get ('title'));
			$page->set ('about', $text->get ('about'));

			$showform = true;

			// Check for input
			$shop = Core_Tools::getInput ('_POST', 'shop', 'varchar');
			$adres = Core_Tools::getInput ('_POST', 'adres', 'varchar');
			$postcode = Core_Tools::getInput ('_POST', 'postcode', 'varchar');
			$gemeente = Core_Tools::getInput ('_POST', 'gemeente', 'varchar');
			$email = Core_Tools::getInput ('_POST', 'email', 'varchar');

			// Company id
			$cid = Core_Tools::getInput ('_GET', 'cid', 'int');

			if ($shop)
			{
				// Let's add the shop
				$sid = $db->insert
				(
					'shops',
					array
					(
						's_name' => $shop,
						's_adres' => $adres,
						's_postcode' => $postcode,
						's_gemeente' => $gemeente,
						's_email' => $email
					)
				);

				// Add you as a moderator
				$db->insert
				(
					'players_shop',
					array
					(
						'plid' => $login->getUserId (),
						's_id' => $sid
					)
				);

				// Now let's add the company (if set)
				$company = Profile_Company::getCompany ($cid);
				$myself = Profile_Member::getMyself ();
				
				if (
					$cid > 0 &&
					$company->isFound () &&
					$myself &&
					(
						$company->getUserStatus ($myself) == 'moderator' ||
						$company->getUserStatus ($myself) == 'administrator'
					)
				)
				{

					$showform = false;

					$company->addShop ($sid);

					header ('Location: '.self::getUrl ('page=shop&id='.$sid.'&action=manage'));
				}
				
				return '<p>'.$text->get ('done').'</p>';
			}
			elseif ($shop || $adres || $postcode || $gemeente || $email)
			{
				$warning = $text->get ('completeForm');
			}

			if ($showform)
			{
				$page = new Core_Template ();

				if (isset ($warning))
				{
					$page->set ('warning', $warning);
				}

				$page->set ('shop_value', Core_Tools::output_form ($shop));
				$page->set ('adres_value', Core_Tools::output_form ($adres));
				$page->set ('postcode_value', Core_Tools::output_form ($postcode));
				$page->set ('gemeente_value', Core_Tools::output_form ($gemeente));
				$page->set ('email_value', Core_Tools::output_form ($email));

				$page->set ('title', $text->get ('title'));
				$page->set ('about', $text->get ('about'));

				$page->set ('submit', $text->get ('submit'));
				
				$page->set ('shop', $text->get ('shop'));
				$page->set ('adres', $text->get ('adres'));
				$page->set ('postcode', $text->get ('postcode'));
				$page->set ('gemeente', $text->get ('gemeente'));
				$page->set ('email', $text->get ('email'));

				$page->set ('formAction', self::getUrl ('page=shop&action=add&cid='.$cid));
			}

			return $page->parse ('shop_add.tpl');
		}
		else
		{
			return '<p>'.$text->get ('noLogin').'</p>';
		}
	}
	
	private function getModeratorManagement ($shop)
	{
		$page = new Core_Template ();
		$text = Core_Text::__getInstance ();
		
		// Add a moderator
		$email = Core_Tools::getInput ('_POST', 'moderator_mail', 'varchar');
		if ($email)
		{
			// Check for player
			$objUser = Profile_Member::getFromEmail ($email);
			if ($objUser)
			{
				$shop->addModerator ($objUser);
			}
			else
			{
				$page->set ('error', 'Er is geen gebruiker gevonden met dit email adres.');
			}
		}
		
		// Remove moderator
		$id = Core_Tools::getInput ('_GET', 'plid', 'int');
		$do = Core_Tools::getInput ('_GET', 'do', 'varchar');
		if ($id && $do)
		{
			$mod = Profile_Member::getMember ($id);
			$myself = Profile_Member::getMyself ();
			
			if ($mod && $myself && $myself->getId () != $mod->getId ())
			{
				$shop->removeModerator ($mod);
			}
			else
			{
				$page->set ('error', 'Deze gebruiker is niet gevonden. Je kan jezelf niet verwijderen.');
			}
		}
		
		// Show
		$page->set ('action', $this->getUrl ('page=shop&id='.$shop->getId().'&action=moderator'));
		
		$page->set
		(
			'title',
			Core_Tools::putIntoText
			(
				$text->get ('title', 'overview', 'shop'),
				array
				(
					Core_Tools::output_varchar ($shop->getName ())
				)
			)
		);
		
		// Show all moderators
		foreach ($shop->getModerators () as $v)
		{
			$page->addListValue
			(
				'moderators',
				array
				(
					'name' => Core_Tools::output_varchar ($v->getFullname ()),
					'url' => 'mailto:'.$v->getEmail (),
					'removeUrl' => $this->getUrl ('page=shop&id='.$shop->getId().'&action=moderator&do=remove&plid='.$v->getId ())
				)
			);
		}
		
		return $page->parse ('shop_moderators.tpl');
	}

	public function getOverview ($shop)
	{
		/*
		$login = Core_Login::__getInstance ();
		$db = Core_Database::__getInstance ();
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('shop');
		$text->setSection ('overview');

		$page = new Core_Template ();

		// Fetch the title
		$page->set
		(
			'title',
			Core_Tools::putIntoText
			(
				$text->get ('title'),
				array
				(
					Core_Tools::output_varchar ($objShop->getName ())
				)
			)
		);

		$page->set ('products', $text->get ('products'));
		$page->set ('noProducts', $text->get ('noProducts'));

		// Loop products
		$products = $objShop->getProducts ();

		foreach ($products as $v)
		{
			$page->addListValue
			(
				'products',
				array
				(
					Core_Tools::output_varchar ($v['p_name']),
					Core_Tools::output_varchar ($v['p_info']),
					$v['p_price']
				)
			);
		}

		$page->set ('currency', Core_Tools::output_varchar ($objShop->getCurrency ()));

		return $page->parse ('shop_overview.tpl');
		*/
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('products');

		$page = new Core_Template ();
		
		if ($shop->canModerate (Profile_Member::getMyself ()))
		{
			$page->set ('manage_url', self::getUrl ('page=shop&id='.$shop->getId().'&action=manage'));
			$page->set ('moderator_url', self::getUrl ('page=shop&id='.$shop->getId().'&action=moderator'));
		}

		$page->set
		(
			'title',
			Core_Tools::putIntoText
			(
				$text->get ('title', 'overview', 'shop'),
				array
				(
					Core_Tools::output_varchar ($shop->getName ())
				)
			)
		);

		if (isset ($poefboek)) {
			$page->set (
				'poefboek',
				Core_Tools::putIntoText
				(
					$text->get ('poefboek'),
					array
					(
						$poefboek,
						$shop->getCurrency ()
					)
				)
			);

			$page->set ('poefboek_value', $poefboek);
		}
		
		$categories = $shop->getCategories (true);

		$page->set ('order', $text->get ('order', 'products', 'order'));
		$page->set ('currency', $shop->getCurrency ());
		$page->set ('noProducts', $text->get ('noProducts'));
		$page->set ('products', $text->get ('products'));

		$page->set ('message', Core_Tools::output_text ($shop->getMessage ()));
		
		$products = $shop->getProducts ();
		foreach ($products as $v)
		{
			if (!isset ($catProducts[$v['c_id']]))
			{
				$catProducts[$v['c_id']] = array ();
			}
			
			$catProducts[$v['c_id']][] = $v;
		}
		
		foreach ($categories as $category)
		{

			$orderUrl = array ();
			
			$newProducts = array ();
			if (isset ($catProducts[$category['c_id']])) 
			{
				foreach ($catProducts[$category['c_id']] as $v)
				{
					$oUrls = array ();
					foreach ($orderUrl as $key => $url)
					{
						$oUrls[$key] = self::getUrl ($url . '&pid='.$v['p_id']);
					}
				
					$newProducts[] = array
					(
						Core_Tools::output_varchar ($v['p_name']),
						Core_Tools::output_varchar ($v['p_info']),
						$v['prices'],
						$oUrls
					);
				}
			}
			
			$page->addListValue
			(
				'categories',
				array
				(
					'name' => $category['c_name'],
					'products' => $newProducts,
					'prices' => $category['prices']
				)
			);
		}
		
		// Show all moderators
		foreach ($shop->getModerators () as $v)
		{
			$page->addListValue
			(
				'moderators',
				array
				(
					'name' => Core_Tools::output_varchar ($v->getFullname ()),
					'url' => 'mailto:'.$v->getEmail ()
				)
			);
		}
	
		return $page->parse ('shop_products.tpl');
	}
	
	public function getManageCategories ($objShop)
	{	
		$login = Core_Login::__getInstance ();
		$db = Core_Database::__getInstance ();
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('shop');
		$text->setSection ('categories');
		
		// Insert new category
		$newcat = Core_Tools::getInput ('_POST', 'catname', 'username', false);
		if ($newcat)
		{
			$db->insert
			(
				'categories',
				array
				(
					's_id' => $objShop->getId (),
					'c_name' => $newcat
				)
			);
		}
		
		// Check for remove
		$remove = Core_Tools::getInput ('_GET', 'remove', 'int');
		if ($remove > 0)
		{
			$chk = $db->remove
			(
				'categories',
				"c_id = '".$remove."' AND s_id = '".$objShop->getId()."'"
			);
			
			if ($chk == 1)
			{
				$db->remove
				(
					'categories_prices',
					"c_id = '".$remove."'"
				);
			}
		}
		
		// Initialize page
		$page = new Core_Template ();
		
		$page->set
		(
			'title',
			Core_Tools::putIntoText
			(
				$text->get ('title'),
				array
				(
					Core_Tools::output_varchar ($objShop->getName ())
				)
			)
		);
		
		// Get a list of categories
		$l = $db->select
		(
			'categories',
			array ('*'),
			"s_id = '".$objShop->getId()."'"
		);
		
		foreach ($l as $v)
		{
			// Check for input prices
			$okay = true;
			$i = 0;
			
			$updates = array ();
			while ($okay && $i < 10)
			{
				$input = Core_Tools::getInput ('_POST', 'price_'.$v['c_id'].'_'.$i, 'varchar');
				if (!empty ($input))
				{
					$updates[$i] = $input;
				}
				$okay = !empty ($input);
				
				$i ++;
			}
			
			// Only process this if true
			if (count ($updates) > 0)
			{
				$db->remove ('categories_prices', "c_id = '".$v['c_id']."'");
				
				foreach ($updates as $catId => $catName)
				{
					$db->insert
					(
						'categories_prices',
						array
						(
							'c_id' => $v['c_id'],
							'p_id' => $catId,
							'c_name' => $catName
						)
					);
				}
			}
			
			// Check for new price
			$input = Core_Tools::getInput ('_POST', 'price_'.$v['c_id'].'_'.($i), 'varchar');
			if ($input)
			{
				$db->insert
				(
					'categories_prices',
					array
					(
						'c_id' => $v['c_id'],
						'p_id' => ($i-1),
						'c_name' => $input
					)
				);
			}
			
			// Fetch the prices
			$prices = array ();
			
			$p = $db->select
			(
				'categories_prices',
				array ('*'),
				"c_id = '".$v['c_id']."'"
			);
			
			$maxi = 0;
			foreach ($p as $vv)
			{				
				$prices[] = array 
				(
					'id' => $v['c_id'],
					'name' => Core_Tools::output_varchar ($vv['c_name'])
				);
			}
			
			$page->addListValue
			(
				'cats',
				array
				(
					'name' => Core_Tools::output_varchar ($v['c_name']),
					'id' => $v['c_id'],
					'prices' => $prices,
					'remUrl' => Pages_Page::getUrl ('page=shop&id='.$objShop->getId().'&action=categories&remove='.$v['c_id'])
				)
			);
		}
		
		$page->set ('addcat', $text->get ('addcat'));
		$page->set ('addcat_url', self::getUrl ('page=shop&id='.$objShop->getId ().'&action=categories'));
		
		$page->set ('catname', $text->get ('catname'));
		$page->set ('addsubmit', $text->get ('addsubmit'));
		$page->set ('cats', $text->get ('cats'));
		$page->set ('nocats', $text->get ('nocats'));
		$page->set ('prices', $text->get ('prices'));
		$page->set ('newPrice', $text->get ('newPrice'));
		$page->set ('savePrices', $text->get ('savePrices'));
		$page->set ('remove', $text->get ('remove'));
		$page->set ('youSure', addslashes ($text->get ('youSure')));

		$page->set ('about', $text->get ('about'));
		
		return $page->parse ('shop_categories.tpl');
	}

	public function getShopManagement ($objShop)
	{
		$login = Core_Login::__getInstance ();
		$db = Core_Database::__getInstance ();
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('shop');
		$text->setSection ('manager');

		// Check for input
		$input = Core_Tools::getInput ('_POST', 'submit', 'varchar');
		if ($input == 'saveSettings')
		{
			$this->processManagementInput ($objShop);
		}

		$page = new Core_Template ();
		
		$categories = $objShop->getCategories ();
		
		$colsToShow = $objShop->getMaxPricesPerCategory ($categories);
		
		foreach ($categories as $v)
		{
			$page->addListValue
			(
				'categories',
				array
				(
					'id' => $v['c_id'],
					'name' => Core_Tools::output_varchar ($v['c_name'])
				)
			);
		}
		
		$page->set ('priceColsToShow', $colsToShow);

		$page->set
		(
			'title',
			Core_Tools::putIntoText
			(
				$text->get ('title'),
				array
				(
					Core_Tools::output_varchar ($objShop->getName ())
				)
			)
		);

		$page->set ('message', $text->get ('message'));
		$page->set ('message_value', Core_Tools::output_form ($objShop->getMessage ()));

		$page->set ('products', $text->get ('products'));
		$page->set ('productName', $text->get ('productName'));
		$page->set ('productText', $text->get ('productText'));
		$page->set ('productPrice', $text->get ('productPrice'));
		$page->set ('noRows', $text->get ('noRows'));
		$page->set ('submit', $text->get ('submit'));

		$page->set ('formAction', self::getUrl ('page=shop&id='.$objShop->getId ().'&action=manage'));

		// Add row link
		$page->set ('toAddRow', $text->getClickTo ($text->get ('toAddRow')));
		$page->set ('addRow', $text->get ('addRow'));
		
		$page->set ('editCategory', $text->get ('editCategory'));
		$page->set ('editCategory_url', self::getUrl ('page=shop&id='.$objShop->getId ().'&action=categories'));

		// Loop products
		$products = $objShop->getProducts ();
		foreach ($products as $v)
		{
			// Fetch the prices			
			$page->addListValue
			(
				'products',
				array
				(
					$v['p_id'],
					Core_Tools::output_form ($v['p_name']),
					Core_Tools::output_form ($v['p_info']),
					$v['prices'],
					$v['c_id']
				)
			);
		}

		return $page->parse ('shop_manage.tpl');
	}

	private function processManagementInput ($objShop)
	{
		$db = Core_Database::__getInstance ();
	
		$iCount = 0;
		while ($iCount !== false && $iCount >= 0 && $iCount < 200)
		{
			$iCount ++;
		
			$productName = Core_Tools::getInput ('_POST', 'productName'.$iCount, 'varchar');
			$productText = Core_Tools::getInput ('_POST', 'productText'.$iCount, 'varchar');
			$originalId = Core_Tools::getInput ('_POST', 'productOrg'.$iCount, 'varchar');
			$categoryId = Core_Tools::getInput ('_POST', 'categoryId'.$iCount, 'varchar');
			
			$okay = true;
			$productPrice = "";
			$i = 0;
			
			while ($okay && $i < 10)
			{
				$priceIn = Core_Tools::getInput ('_POST', 'productPrice'.$iCount.'_'.$i, 'float');
				if ($priceIn > 0)
				{
					$productPrice .= $priceIn . ',';
				}
				else
				{
					$okay = false;
				}
				
				$i ++;
			}
			
			$productPrice = substr ($productPrice, 0, -1);

			/* 2 requirements: name & price */
			if ($productName)
			{
				if ($originalId > 0)
				{
					// update
					$db->update
					(
						'products',
						array
						(
							'p_name' => $productName,
							'p_info' => $productText,
							'p_price' => $productPrice,
							'c_id' => $categoryId
						),
						"p_id = '".$originalId."' AND s_id = '".$objShop->getId ()."'"
					);
				}
				else
				{
					// add
					$db->insert
					(
						'products',
						array
						(
							'p_name' => $productName,
							'p_info' => $productText,
							'p_price' => $productPrice,
							's_id' => $objShop->getId (),
							'c_id' => $categoryId
						)
					);
				}
			}
			elseif ($originalId > 0)
			{
				$db->remove
				(
					'products',
					"p_id = '".$originalId."' AND s_id = '".$objShop->getId ()."'"
				);
			}
			else
			{
				// Get out of here!
				$iCount = false;
			}
		}

		// Last: general info
		$message = Core_Tools::getInput ('_POST', 'shopMessage', 'varchar');
		$db->update
		(
			'shops',
			array
			(
				's_message' => $message
			),
			"s_id = '".$objShop->getId ()."'"
		);

		$objShop->refreshData ();
	}
}
?>
