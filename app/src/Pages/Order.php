<?php
class Pages_Order extends Pages_Page
{
	protected function getContent ()
	{
		$sid = Core_Tools::getInput ('_GET', 'sid', 'int');
		$cid = Core_Tools::getInput ('_GET', 'cid', 'int');
		$id = Core_Tools::getInput ('_GET', 'id', 'int');
		$action = Core_Tools::getInput ('_GET', 'action', 'varchar');
		$orderId = Core_Tools::getInput ('_GET', 'oid', 'int');

		$login = Core_Login::__getInstance ();

		if ($sid && $cid && $login->isLogin ())
		{
			return $this->getOrderPage ($cid, $sid);
		}
		elseif ($orderId)
		{
			return $this->getOrderPrint ($orderId);
		}
		elseif ($action == 'submit')
		{
			return $this->getSubmitOrder ($id);
		}
		else
		{
			return $this->getChooseCompany ($id);
		}
	}

	private function getChooseCompany ()
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('choose');

		$page = new Core_Template ();

		$page->set ('title', $text->get ('title'));
		$page->set ('about', $text->get ('about'));
		$page->set ('noCompanies', $text->get ('noCompanies'));

		$myself = Profile_Member::getMyself ();
		if ($myself && $myself->isFound ())
		{
			$companies = $myself->getMyCompanies ();

			foreach ($companies as $company)
			{
				// Fetch the shops
				$o = array ();
				foreach ($company->getShops () as $shop)
				{
					$o[] = array
					(
						Core_Tools::output_varchar ($shop->getName ()),
						$shop->getId (),
						self::getUrl ('page=order&cid='.$company->getId ().'&sid='.$shop->getId ())
					);
				}

				if (count ($o) > 0)
				{
					$page->addListValue
					(
						'companies',
						array
						(
							Core_Tools::output_varchar ($company->getName ()),
							$company->getId (),
							$o
						)
					);
				}
			}

			// Show pending orders
 			$page->set ('pendingOrders', $this->getPendingOrders ($myself));
		}
		
		// Especially for google, we're going to show a list of shops here)
		else
		{
			//$page->set ('notLoggedIn', $text->get ('notLoggedIn'));
			return $this->getGoogleContent ();
		}
		
		return $page->parse ('order_choose.tpl');
	}

	private function getOrderPage ($cid, $sid)
	{
		$myself = Profile_Member::getMyself ();
		
		// Check input
		$company = Profile_Company::getCompany ($cid);
		$shop = Profile_Shop::getShop ($sid);

		if
		(
			$company->getUserStatus ($myself) >= 0
			&& $company->hasShop ($shop)
		)
		{
			$pid = Core_Tools::getInput ('_GET', 'pid', 'int');
			if ($pid && $shop->hasProduct ($pid))
			{
				return $this->getAddProduct ($company, $shop, $myself, $pid);
			}
			else
			{
				return $this->getChooseProduct ($company, $shop);
			}
		}
		else
		{
			header ('Location: '.self::getUrl ('page=order'));
			return '<p>Invalid input.</p>';
		}
	}

	private function getChooseProduct ($company, $shop)
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('products');

		$page = new Core_Template ();

		$this->setPageTitle ($page, $company, $shop);

		// Fetch thze poefboek
		$myself = Profile_Member::getMyself ();
		$poefboek = $company->getMemberPoefboek ($myself);
			
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
		
		$categories = $shop->getCategories (true);
		
		$page->set ('poefboek_value', $poefboek);

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
			
			foreach ($category['prices'] as $price)
			{
				$orderUrl[$price['p_id']] = 'page=order&cid='.$company->getId ().'&sid='.$shop->getId ().'&price='.$price['p_id'];
			}

			
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
	
		return $page->parse ('order_products.tpl');
	}

	private function setPageTitle ($page, $company, $shop)
	{
		$text = Core_Text::__getInstance ();

		$page->set
		(
			'title',
			Core_Tools::putIntoText
			(
				$text->get ('title', 'main', 'order'),
				array
				(
					Core_Tools::output_varchar ($company->getName ()),
					Core_Tools::output_varchar ($shop->getName ())
				)
			)
		);
	}

	private function getAddProduct ($company, $shop, $myself, $pid)
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('addProduct');
	
		// Throw a form with additional text message etc.
		$confirm = Core_Tools::getInput ('_POST', 'submit', 'varchar');
		$message = Core_Tools::getInput ('_POST', 'message', 'varchar');
		$amount = Core_Tools::getInput ('_POST', 'amount', 'varchar');
		$priceIn = Core_Tools::getInput ('_POST', 'price', 'int');

		$page = new Core_Template ();

		$this->setPageTitle ($page, $company, $shop);
		$page->set ('back', $text->get ('back'));
		$page->set ('back_url', self::getUrl ('page=order&cid='.$company->getId ().'&sid='.$shop->getId ()));

		$product = $shop->getProduct ($pid);
		
		if ($product)
		{
			$categories = $shop->getCategories ();
			
			$price = Core_Tools::getInput ('_GET', 'price', 'int');
			
			if (isset ($categories[$product['c_id']]))
			{
				foreach ($categories[$product['c_id']]['prices'] as $v)
				{
					$page->addListValue
					(
						'prices',
						array
						(
							'id' => $v['p_id'],
							'name' => $v['c_name'],
							'price' => $product['prices'][$v['p_id']],
							'checked' => $price == $v['p_id']
						)
					);
				}
			}

			if 
			(
				$confirm && 
				$amount > 0 && $amount < 10 && 
				isset ($product['prices'][$priceIn]) && 
				$product['prices'][$priceIn] !== null
			)
			{
				$page->set ('done', $text->get ('done'));

				// Fetch the "pending" order ID
				$orderID = $this->getCurrentOrderId ($company, $shop);

				// Add the order to the database
				$db = Core_Database::__getInstance ();
				
				$db->insert
				(
					'order_prods',
					array
					(
						'o_id' => $orderID,
						'p_id' => (int)$pid,
						'p_pid' => (int)$priceIn,
						'plid' => $myself->getId (),
						'op_message' => $message,
						'op_amount' => $amount,
						'op_price' => $product['prices'][$priceIn]
					)
				);
			}
			else
			{
				$page->set ('about', $text->get ('about'));

				$page->set ('product', Core_Tools::output_varchar ($product['p_name']));
				$page->set ('details', Core_Tools::output_varchar ($product['p_info']));				
				
			}
		}

		else
		{
			return '<p>Invalid input: product not found.</p>';
		}

		return $page->parse ('order_add.tpl');
	}

	private function getCurrentOrderId ($company, $shop)
	{
		// Create (or find) current order ID
		$db = Core_Database::__getInstance ();

		$orderId = $db->select
		(
			'orders',
			array ('o_id'),
			"c_id = '".$company->getId ()."' && s_id = '".$shop->getId ()."' AND o_isDone = '0'"
		);

		if (count ($orderId) > 0)
		{
			return $orderId[0]['o_id'];
		}
		else
		{
			return $db->insert
			(
				'orders',
				array
				(
					'c_id' => $company->getId (),
					's_id' => $shop->getId (),
					'o_isDone' => '0'
				)
			);
		}
		
	}

	private function getPendingOrders ($myself)
	{
		// Check for mastery face
		$db = Core_Database::__getInstance ();
		$text = Core_Text::__getInstance ();

		$l = $db->select
		(
			'players_comp',
			array ('*'),
			"plid = '".$myself->getId ()."' AND compStatus > 0 AND isApproved = '1'"
		);

		if (count ($l) > 0)
		{
			// Fetch the pending orders
			$l = $db->getDataFromQuery ($db->customQuery
			("
				SELECT
					*,COUNT(order_prods.op_id) AS aantal
				FROM
					players_comp
				LEFT JOIN
					orders ON orders.c_id = players_comp.c_id
				LEFT JOIN
					order_prods ON order_prods.o_id = orders.o_id
				LEFT JOIN
					companies ON companies.c_id = orders.c_id AND players_comp.c_id = companies.c_id
				WHERE
					players_comp.plid = '".$myself->getId ()."'
					AND players_comp.compStatus > 0
					AND players_comp.isApproved = 1
					AND orders.o_isDone = '0'
				GROUP BY
					orders.o_id
				HAVING
					aantal > 0
			"));

			if (count ($l) > 0)
			{
				$page = new Core_Template ();

				$page->set ('title', $text->get ('title', 'submit', 'order'));
				foreach ($l as $v)
				{
					$company = Profile_Company::getCompany ($v['c_id']);
					$company->setData ($v);

					$shop = Profile_Shop::getShop ($v['s_id']);

					$page->addListValue
					(
						'orders',
						array
						(
							Core_Tools::output_varchar ($company->getName ()),
							Core_Tools::output_varchar ($shop->getName ()),
							Core_Tools::putIntoText
							(
								$text->get ('products', 'submit', 'order'),
								array
								(
									$v['aantal']
								)
							),
							self::getUrl ('page=order&action=submit&id='.$v['o_id'])
						)
					);
				}

				return $page->parse ('blocks/pendingOrders.tpl');
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}

	private function getSubmitOrder ($id)
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('submit');

		$login = Core_Login::__getInstance ();

		if ($login->isLogin ())
		{
			$userID = (int)$login->getUserId ();
		}
		else
		{
			$userID = 0;
		}

		// Fetch the order
		$db = Core_Database::__getInstance ();
		$order = $db->getDataFromQuery ($db->customQuery
		("
			SELECT
				*
			FROM
				orders
			LEFT JOIN
				companies ON orders.c_id = companies.c_id
			LEFT JOIN
				players_comp ON players_comp.c_id = companies.c_id
				AND players_comp.plid = '".$userID."'
			WHERE
				o_id = '".((int)$id)."'
		"));

		if (
			count ($order) == 1 &&
			$order[0]['o_isDone'] == 0 &&
			$order[0]['compStatus'] > 0
		)
		{
			$key = Core_Tools::getInput ('_POST', 'confirmKey', 'varchar');

			if ($key && Core_Tools::checkConfirmLink ($key))
			{
				$this->doSubmit ($order[0]);
				return $this->getOrderPrint ($order[0]['o_id']);
			}
			else
			{
				return $this->getSubmitForm ($order[0]);
			}

			
		}
		else
		{
			return '<p>Invalid input.</p>';
		}
	}

	private function doSubmit ($order)
	{
		$db = Core_Database::__getInstance ();

		$db->update
		(
			'orders',
			array
			(
				'o_isDone' => 1,
				'o_orderDate' => 'NOW()'
			),
			"o_id = '{$order['o_id']}'"
		);

		$company = Profile_Company::getCompany ($order['c_id']);
		$company->setData ($order);

		// Poefboek stuff!
		$userPoef = array ();

		foreach ($this->getOrderedProducts ($order['o_id']) as $v)
		{
			$price = $v['op_amount'] * $v['op_price'];
			if (isset ($userPoef[$v['plid']]))
			{
				$userPoef[$v['plid']] += $price;
			}
			else
			{
				$userPoef[$v['plid']] = $price;
			}
		}

		// Now do the actual updates
		foreach ($userPoef as $k => $v)
		{
			$company->takeFromMemberPoefboek (Profile_Member::getMember ($k), $v, 'order', $order['o_id']);
		}
	}

	private function sendMail ($order, $email, $toName = "")
	{
		$myself = Profile_Member::getMyself ();
		
		Core_Tools::sendMail 
		(
			$this->getMailSubject ($order), 
			$this->getMailBody ($order), 
			$email, 
			$toName, 
			$myself->getFullname (), 
			$myself->getEmail ()
		);
	}
	
	private function getMailSubject ($order)
	{
		$text = Core_Text::__getInstance ();
		return Core_Tools::putIntoText
		(
			$text->get ('mOrder', 'submit', 'order'),
			array
			(
				Core_Tools::output_varchar ($order['c_name']),
				date ('d/M')
			)
		);
	}
	
	private function getMailBody ($order)
	{
		$text = Core_Text::__getInstance ();
		
		$page = new Core_Template ();
		
		$page->set ('order', $this->getOrderOverview ($order, false, false, true));
		$page->set ('footer', $text->get ('printerFooter', 'main', 'main'));
		
		return $page->parse ('mailtemplate.tpl');
	}

	private function getOrderPrint ($id)
	{
		$db = Core_Database::__getInstance ();
		
		$order = $db->getDataFromQuery ($db->customQuery
		("
			SELECT
				*
			FROM
				orders
			LEFT JOIN
				companies ON orders.c_id = companies.c_id
			LEFT JOIN
				players_comp ON players_comp.c_id = companies.c_id
			LEFT JOIN
				shops ON orders.s_id = shops.s_id
			WHERE
				o_id = '".((int)$id)."'
		"));

		if (
			count ($order) == 1 &&
			$order[0]['compStatus'] >= 0
		)
		{

			$email = Core_Tools::getInput ('_POST', 'email', 'email');

			if ($email)
			{
				$this->sendMail ($order[0], $email, $order[0]['s_name']);
			}
		
			return $this->getOrderOverview ($order[0], true, (bool)$email);
		}
		else
		{
			return '<p>Invalid input.</p>';
		}
	}

	private function getSubmitForm ($order)
	{
		$db = Core_Database::__getInstance ();
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('submit');

		$page = new Core_Template ();
	
		$page->set ('order_overview', $this->getOrderOverview ($order));

		$page->set ('sendOrder', $text->get ('submit'));
		$page->set ('confirmSubmit', $text->get ('confirmSubmit'));
		$page->set ('confirmKey', Core_Tools::getConfirmLink ());
		$page->set ('action_url', Pages_Page::getUrl ('page=order&action=submit&id='.$order['o_id']));

		return $page->parse ('order_submit.tpl');
	}

	private function getOrderOverview ($order, $showOnlineVersion = true, $showSended = false, $showNames = true)
	{
		$db = Core_Database::__getInstance ();
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('order');
		$text->setSection ('submit');
	
		$page = new Core_Template ();

		$company = Profile_Company::getCompany ($order['c_id']);
		$company->setData ($order);

		$shop = Profile_Shop::getShop ($order['s_id']);

		$page->set ('company', $text->get ('company'));
		$page->set ('shop', $text->get ('shop'));
		$page->set ('listProducts', $text->get ('listProducts'));
		$page->set ('orderId', $order['o_id']);

		$page->set ('company_adres', $company->getAdress ());
		$page->set ('shop_adres', $shop->getAdress ());
		$page->set ('currency', Core_Tools::output_varchar ($shop->getCurrency ()));

		$page->set ('thanks', $text->get ('thanks'));

		if ($showOnlineVersion && $order['o_isDone'] == 1)
		{
			$page->set ('sendMail', $text->get ('sendMail'));
			$page->set ('email', $text->get ('email'));
			$page->set ('sendIt', $text->get ('sendIt'));
			$page->set ('printIt', $text->get ('printIt'));

			if ($showSended)
			{
				$page->set ('sended', $text->get ('sended'));
			}

			$page->set ('mail_action', Pages_Page::getUrl ('page=order&oid='.$order['o_id']));
		}

		$products = $this->getOrderedProducts ($order['o_id']);
		
		$page->set ('table_cols', $showOnlineVersion ? 3 : 2);

		// Fetch the categories for this shop
		$categories = $shop->getCategories ();

		foreach ($products as $v)
		{
			if ($v['op_amount'] > 1)
			{
				$name = $v['op_amount'] . ' x ' . Core_Tools::output_varchar ($v['p_name']);
			}
			else
			{
				$name = Core_Tools::output_varchar ($v['p_name']);
			}
		
			// Fetch price name
			$catname = '';
			if (isset ($categories[$v['c_id']]) && count ($categories[$v['c_id']]['prices']) > 1)
			{
				if (isset ($categories[$v['c_id']]['prices'][$v['p_pid']]))
				{
					$catname = ' ('.$categories[$v['c_id']]['prices'][$v['p_pid']]['c_name'].')';
				}
			}

			$page->addListValue
			(
				'products',
				array
				(
					$name,
					($showNames ? Core_Tools::output_varchar ($v['realname']) : null),
					Core_Tools::convert_price ($v['op_amount'] * $v['op_price']),
					$v['op_message'],
					$catname
				)
			);
		}

		return $page->parse ('order_view.tpl');
	}

	private function getOrderedProducts ($oid)
	{
		$db = Core_Database::__getInstance ();
		
		return $db->getDataFromQuery ($db->customQuery
		("
			SELECT
				*
			FROM
				order_prods
			LEFT JOIN
				products ON order_prods.p_id = products.p_id
			LEFT JOIN
				players ON order_prods.plid = players.plid
			WHERE
				order_prods.o_id = $oid
			ORDER BY
				order_prods.plid
		"));
	}
	
	/*
		This is what google sees when someone orders something
		(= when you follow the "order" when you're not logged in)
	*/
	public function getGoogleContent ()
	{
		$db = Core_Database::__getInstance ();
	
		$shop = Core_Tools::getInput ('_GET', 'sid', 'int', false);
		
		if ($shop > 0)
		{
			// Show all products of this shop
			$shop = Profile_Shop::getShop ($shop);
			
			if ($shop)
			{
				$shop_page = new Pages_Shop ();
				return $shop_page->getOverview ($shop);
			}
		}

		// Show a list of all shops.
		$page = new Core_Template ();
		
		$data = $db->select
		(
			'shops',
			array ('*'),
			null,
			's_name ASC'
		);
		
		foreach ($data as $v)
		{
			$page->addListValue
			(
				'shops',
				array
				(
					'name' => Core_Tools::output_varchar ($v['s_name']),
					'url' => self::getUrl ('page=order&sid='.$v['s_id']),
					'location' => Core_Tools::output_varchar ($v['s_gemeente'])
				)
			);
		}
		
		return $page->parse ('google_shops.phpt');
	}
}
?>
