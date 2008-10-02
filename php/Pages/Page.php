<?php

class Pages_Page
{

	public static function getPage ($page)
	{
		$pagename = 'Pages_'.ucfirst ($page);
		if (class_exists ($pagename))
		{
			return new $pagename ($page);
		}
		else
		{
			return new Pages_Error404 ($page);
		}
	}
	
	/* Global HTML fetcher (do not overwrite unless requesting a whole different page) */
	public function getHTML ()
	{
		$page = new Core_Template ();
		
		// Login first
		$page->set ('login', $this->getLogin ());
		$page->set ('title', $this->getTitle ());
		$page->set ('footer', $this->getFooter ());
		$page->set ('content', $this->getContent ());
		$page->set ('menu', $this->getMenu ());

		// Order baskes
		$page->set ('basket', $this->getBasket ());

		$text = Core_Text::__getInstance ();

		// Languages:
		$langs = getLanguages ();
		foreach ($langs as $v)
		{
			$page->addListValue
			(
				'languages',
				array
				(
					$text->get ($v[0], 'languages', 'main', $v[0]),
					self::getUrl ('page=home&language='.$v[1])
				)
			);
		}

		$langs = getLayouts ();
		foreach ($langs as $v)
		{
			$page->addListValue
			(
				'layouts',
				array
				(
					$text->get ($v[0], 'layouts', 'main', $v[0]),
					self::getUrl ('page=home&layout='.$v[1])
				)
			);
		}

		$page->set ('languages', $text->get ('languages', 'main', 'main'));
		$page->set ('layouts', $text->get ('layout', 'main', 'main'));
		$page->set ('footerSpoof', $text->get ('footer', 'main', 'main'));

		$page->set ('printerFooter', $text->get ('printerFooter', 'main', 'main'));

		$page->sortList ('languages');
		
		$db = Core_Database::__getInstance ();
		$page->set ('mysqlCount', $db->getCounter ());
		
		return $page->parse ('index.tpl');
	}

	/* Various */
	public static function getUrl ($url)
	{
		return STATIC_URL . '?' . $url;
	}

	/* Page specific functions */
	protected function getContent ()
	{
		return null;
	}
	
	protected function getTitle ()
	{
		$text = Core_Text::__getInstance ();
		return $text->get ('title', 'main', 'main');
	}
	
	protected function getFooter ()
	{
		return '&copy; <a href="http://www.daedeloth.be/">Thijs Van der Schaeghe</a>'.
			', <a href="http://www.neuroninteractive.eu/">Neuron Interactive</a> '
			. (date ('Y') == 2007 ? '2007' : '2007 - '.date ('Y'));
	}
	
	protected function getMenu ()
	{
		$login = Core_Login::__getInstance ();
		$text = Core_Text::__getInstance ();
		
		$page = new Core_Template ();

		// Let's go for the menu
		if ($login->isLogin ())
		{
			$menu = array
			(
				array ('home', 'page=home'),
				/* array ('about', 'page=about'), */
				array ('company', 'page=register'),
				array ('order', 'page=order')
			);
		}
		else
		{
			$menu = array
			(
				array ('home', 'page=home'),
				array ('about', 'page=about'),
				array ('register', 'page=register')
			);
		}

		foreach ($menu as $v)
		{
			$page->addListValue
			(
				'menu',
				array
				(
					$text->get ($v[0], 'menu', 'main'),
					self::getUrl ($v[1])
				)
			);
		}
		
		// Some absolute URL's
		$page->addListValue
		(
			'menu2',
			array
			(
				'Forum',
				'http://forum.neuroninteractive.eu/index.php?c=2'
			)
		);
		
		$page->addListValue
		(
			'menu2',
			array
			(
				'Donate',
				'http://forum.neuroninteractive.eu/index.php?c=2'
			)
		);


		return $page->parse ('blocks/menu.tpl');
	}

	protected function getLogin ()
	{
		$login = Core_Login::__getInstance ();
		$text = Core_Text::__getInstance ();

		$page = new Core_Template ();

		// Check for login input
		$username = Core_Tools::getInput ('_POST', 'email', 'varchar');
		$password = Core_Tools::getInput ('_POST', 'password', 'varchar');

		$page->set ('login', $text->get ('login', 'login', 'main'));
		$page->set ('logout', $text->get ('logout', 'login', 'main'));
		$page->set ('logout_url', self::getUrl ($_SERVER['QUERY_STRING'].'&logout=true'));
		
		if ($username && $password)
		{
			if (!$login->login ($username, $password))
			{
				$page->set
				(
					'warning',
					$text->get
					(
						$login->getWarnings (),
						'login',
						'main',
						$login->getWarnings ()
					)
				);
			}
		}

		if ($login->isLogin ())
		{
			// Already logged in.
			$page->set ('isLogin', true);

			$myself = Profile_Member::getMyself ();
			$page->set 
			(
				'myName', 
				Core_Tools::putIntoText 
				(
					$text->get ('welcome', 'login', 'main'),
					array
					(
						Core_Tools::output_varchar ($myself->getUsername ())
					)
				)
			);
		}
		else
		{
			// Login form
			$page->set ('isLogin', false);

			$page->set ('email', $text->get ('email', 'login', 'main'));
			$page->set ('password', $text->get ('password', 'login', 'main'));
			$page->set ('submit', $text->get ('submit', 'login', 'main'));
		}

		return $page->parse ('blocks/login.tpl');
	}

	protected function getBasket ()
	{
		$myself = Profile_Member::getMyself ();
		if ($myself)
		{
			$orders = $myself->getPendingOrders ();

			if (count ($orders) > 0)
			{
				// Process Input
				$remId = Core_Tools::getInput ('_GET', 'bRem', 'int');
				if ($remId > 0)
				{
					$myself->removeProductFromBasket ($remId);

					// Reload ;-)
					$orders = $myself->getPendingOrders ();
				}
			
				$text = Core_Text::__getInstance ();
				$page = new Core_Template ();

				$page->set ('remove', addslashes ($text->get('remove', 'basket', 'main')));

				$page->set ('title', $text->get ('title', 'basket', 'main'));

				foreach ($orders as $v)
				{
					// Create combination string
					$comb = $v[1]['op_amount'] . 'x ' . Core_Tools::output_varchar ($v[0]['p_name']) . ", ";
					$comb.= !empty ($v[1]['op_message']) ? Core_Tools::output_varchar ($v[1]['op_message']) . ", " : null;
					$comb.= Core_Tools::output_varchar ($v[2]->getName ()) . ", ";
					$comb.= Core_Tools::output_varchar ($v[3]->getName ());
				
					$page->addListValue
					(
						'products',
						array
						(
							Core_Tools::output_varchar ($v[0]['p_name']),
							Core_Tools::output_varchar ($v[1]['op_amount']),
							Core_Tools::output_varchar ($v[1]['op_message']),
							Core_Tools::output_varchar ($v[1]['op_price']),
							Core_Tools::output_varchar ($v[2]->getName ()),
							Core_Tools::output_varchar ($v[3]->getName ()),
							addslashes ($comb),
							self::getUrl ('page=register&bRem='.$v[1]['op_id'])
						)
					);
				}

				return $page->parse ('blocks/basket.tpl');
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
}
?>
