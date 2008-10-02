<?php
class Pages_Company extends Pages_Page
{
	protected function getContent ()
	{
		$action = Core_Tools::getInput ('_GET', 'action', 'varchar');
		$id = Core_Tools::getInput ('_GET', 'id', 'int');
		
		// Search for company:
		$objCompany = Profile_Company::getCompany ($id);
		
		$login = Core_Login::__getInstance ();

		if ($action == 'add' && $login->isLogin ())
		{
			return $this->getAddCompany ();
		}
		elseif ($action == 'shopman' && $objCompany->isFound ())
		{
			return $this->getShopManagement ($objCompany);
		}
		elseif ($action == 'userman' && $objCompany->isFound ())
		{
			return $this->getUserManagement ($objCompany);
		}
		elseif ($action == 'poefboek' && $objCompany->isFound ())
		{
			return $this->getPoefboekManagement ($objCompany);
		}
		elseif ($action == 'poeflog' && $objCompany->isFound ())
		{
			return $this->getPoefboekLog ($objCompany);
		}
		else
		{
			return $this->getCompanyOverview ($objCompany);
		}
	}

	private function getAddCompany ()
	{
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('company');
		$text->setSection ('add');

		$db = Core_Database::__getInstance ();
		$login = Core_Login::__getInstance ();

		if ($login->isLogin ())
		{
			$showform = true;

			$company = Core_Tools::getInput ('_POST', 'company', 'varchar', false);
			$adres = Core_Tools::getInput ('_POST', 'adres', 'varchar', false);
			$postcode = Core_Tools::getInput ('_POST', 'postcode', 'varchar', false);
			$gemeente = Core_Tools::getInput ('_POST', 'gemeente', 'varchar', false);

			if ($company && $adres && $postcode && $gemeente)
			{
				
				// Little check
				$l = $db->select
				(
					'companies',
					array ('c_id'),
					"c_name = '".$db->escape ($company)."'"
				);
				
				if (count ($l) > 0)
				{
						$warning = $text->get ('companyFound');
				} 
				else 
				{
					// Let's add the company
					$c_id = $db->insert
					(
						'companies',
						array
						(
							'c_name' => $company,
							'c_adres' => $adres,
							'c_postcode' => $postcode,
							'c_gemeente' => $gemeente
						)
					);
	
					// Now let's add the moderator
					$db->insert
					(
						'players_comp',
						array
						(
							'plid' => $login->getUserId (),
							'c_id' => $c_id,
							'isApproved' => 1,
							'compStatus' => 2
						)
					);
	
					$showForm = false;
	
					// Now let's redirect to company overview
					header ('Location: '.self::getUrl ('page=company&id='.$c_id.'&action=shopman'));
					
					return '<p>'.$text->get ('done').'</p>';
				}
			}
			elseif ($company || $adres || $postcode || $gemeente)
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

				$page->set ('title', $text->get ('title'));
				$page->set ('about', $text->get ('about'));
				$page->set ('contactDetails', $text->get ('contactDetails'));

				$page->set ('submit', $text->get ('submit'));
				$page->set ('company', $text->get ('company'));
				$page->set ('adres', $text->get ('adres'));
				$page->set ('postcode', $text->get ('postcode'));
				$page->set ('gemeente', $text->get ('gemeente'));
				
				$page->set ('formAction', self::getUrl ('page=company&action=add'));
			}

			return $page->parse ('company_add.tpl');
		}
	}
	
	private function getCompanyOverview ($objCompany)
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('company');
		$text->setSection ('overview');
		
		$db = Core_Database::__getInstance ();
		$login = Core_Login::__getInstance ();
		
		$page = new Core_Template ();
		
		if ($objCompany->isFound ())
		{
			// Everything okay, let's just continue our path to destruction.
			$page->set 
			(
				'title', 
				Core_Tools::putIntoText 
				(
					$text->get ('title'), 
					array (Core_Tools::output_varchar ($objCompany->getName ()))
				)
			);
			
			// Let's go for the other data
			$data = $objCompany->getData ();
			
			foreach ($data as $k => $v)
			{
				$page->set ($k, Core_Tools::output_varchar ($v));
			}
			
			$page->set ('adres', $text->get ('adres'));
			$page->set ('naam', $text->get ('naam'));
			$page->set ('users', $text->get ('users'));
			$page->set ('noUsers', $text->get ('noUsers'));
			$page->set ('listusers', $text->get ('listusers'));
			$page->set ('shops', $text->get ('shops'));
			$page->set ('listshops', $text->get ('listshops'));
			$page->set ('noShops', $text->get ('noShops'));
			$page->set ('moderate', $text->get ('moderate'));
			
			$users = $objCompany->getUsers ();
			$page->set ('userAmount', count ($users));
			
			// Get my status in here
			$myself = Profile_Member::getMyself ();
			if ($myself)
			{
				$myStatus = $objCompany->getUserStatus ($myself);
				
				if ($myStatus == 'administrator')
				{
					$page->set ('admin_user_link', self::getUrl ('page=company&id='.$objCompany->getId ().'&action=userman'));
					$page->set ('admin_user', $text->get ('useradmin'));
				}
				
				if ($myStatus == 'moderator' || $myStatus == 'administrator')
				{
					$page->set ('admin_shops_link', self::getUrl ('page=company&id='.$objCompany->getId ().'&action=shopman'));
					$page->set ('admin_shops', $text->get ('shopadmin'));
					
					$page->set ('admin_poefboek_link', self::getUrl ('page=company&id='.$objCompany->getId ().'&action=poefboek'));
					$page->set ('admin_poefboek', $text->get ('poefboek'));
					
					// Poefboek content
					$page->set ('poeftotal', $text->get ('poeftotal'));
					$page->set ('poeftotal_value', '&euro; '.Core_Tools::convert_price ($objCompany->getPoefboekTotal ()));
				}
				
				$showPoefboek = true;
			}
			else
			{
				$showPoefboek = false;
			}
			
			foreach ($users as $v)
			{
				$page->addListValue
				(
					'users',
					array
					(
						Core_Tools::output_varchar ($v[0]->getFullname ()),
						$text->get ($v[1], 'userstatus', 'company', $v[1]),
						($showPoefboek ? Core_Tools::convert_price ($v[2]) : null),
						Pages_Page::getUrl ('page=company&id='.$objCompany->getId ().'&action=poeflog&uid='.$v[0]->getId ())
					)
				);
			}
			
			// Broodjeswinkels
			$shops = $objCompany->getShops ();
			$page->set ('shopAmount', count ($shops));

			foreach ($shops as $v)
			{
				$page->addListValue
				(
					'shops',
					array
					(
						Core_Tools::output_varchar ($v->getName ()),
						($v->canModerate ($myself) ? self::getUrl ('page=shop&id='.$v->getId ().'&action=manage') : null),
						self::getUrl ('page=shop&id='.$v->getId ())
					)
				);
			}
			
		}
		else
		{
			$page->set ('title', $text->get ('notFound'));
			$page->set ('notFound', $text->get ('notFoundA'));
		}
		
		return $page->parse ('company_overview.tpl');
	}
	
	public function getShopManagement ($objCompany)
	{
		$db = Core_Database::__getInstance ();
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('company');
		$text->setSection ('shopman');

		$page = new Core_Template ();

		$page->set ('remove', $text->get ('remove'));
		$page->set ('noShops', $text->get ('noShops'));
		$page->set ('conRem', addslashes ($text->get ('conRem')));
		$page->set ('selectShop', $text->get ('selectShop'));
		$page->set ('submitAdd', $text->get ('submitAdd'));
		$page->set ('conToAdd', addslashes ($text->get ('conToAdd')));
		
		$page->set ('formAction', self::getUrl ('page=company&id='.$objCompany->getId ().'&action=shopman'));
		
		$page->set 
		(
			'title', 
			Core_Tools::putIntoText 
			(
				$text->get ('title'), 
				array (Core_Tools::output_varchar ($objCompany->getName ()))
			)
		);

		// Is this page even accessable?
		$myself = Profile_Member::getMyself ();
		if 
		(
			$myself && 
			(
				$objCompany->getUserStatus ($myself) == 'moderator' 
				|| $objCompany->getUserStatus ($myself) == 'administrator'
			)
		)
		{

			// Check for removes
			$remove = Core_Tools::getInput ('_GET', 'remove', 'int');
			if ($remove > 0)
			{
				$objCompany->removeShop ($remove);
			}

			// Check for adds
			$adds = Core_Tools::getInput ('_POST', 'add', 'int');
			if ($adds > 0)
			{
				$objCompany->addShop ($adds);
			}
		
			$shops = $objCompany->getShops ();
			foreach ($shops as $v)
			{
				$page->addListValue
				(
					'shops',
					array
					(
						Core_Tools::output_varchar ($v->getName ()),
						self::getUrl ('page=shop&id='.$v->getId ()),
						self::getUrl ('page=company&id='.$objCompany->getId ().'&action=shopman&remove='.$v->getId ())
					)
				);
			}

			// Add list of available shops
			$shops = Profile_Shop::getShops ();
			foreach ($shops as $v)
			{
				$page->addListValue
				(
					'addshop',
					array
					(
						Core_Tools::output_varchar ($v->getName (true)),
						$v->getId ()
					)
				);
			}

			$page->set ('addShop_url', self::getUrl ('page=shop&action=add&cid='.$objCompany->getId ()));
			$page->set ('addShop', $text->getClickTo ($text->get ('toAddShop')));
			$page->set ('addShopTitle', $text->get ('addShopTitle'));
		}
		else
		{
			// Throw thze error
			$page->set ('noPermission', $text->get ('noPermission'));
		}
		return $page->parse ('company_shops.tpl');
	}

	public function getUserManagement ($objCompany)
	{
		$db = Core_Database::__getInstance ();
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('company');
		$text->setSection ('userman');

		$page = new Core_Template ();

		$page->set 
		(
			'title', 
			Core_Tools::putIntoText 
			(
				$text->get ('title'), 
				array (Core_Tools::output_varchar ($objCompany->getName ()))
			)
		);
		
		$page->set ('adminWarning', addslashes ($text->get ('adminWarning')));

		$myself = Profile_Member::getMyself ();
		if ($myself && $objCompany->getUserStatus ($myself) == 'administrator')
		{

			// Start with processing the input (since you're allowed to any way ;-))
			$count = 0;
			foreach ($_POST as $k => $v)
			{
				if (substr ($k, 0, 4) == 'user')
				{
					$member = Profile_Member::getMember (substr ($k, 4));
					if ($member->isFound ())
					{
						$objCompany->setUserStatus ($member, $v);
						$count ++;
					}
				}
			}

			if ($count > 0)
			{
				//header ('Location: '.self::getUrl ('page=company&id='.$objCompany->getId ()));
			}
		
			// Get all options
			$statusses = array ();
			$statuses = array
			(
				'user' => $text->get ('user', 'userstatus', 'company', 'user'),
				'moderator' => $text->get ('moderator', 'userstatus', 'company', 'moderator'),
				'administrator' => $text->get ('administrator', 'userstatus', 'company', 'administrator'),
				'pending' => $text->get ('pending', 'userstatus', 'company', 'pending'),
				'remove' => $text->get ('remove')
			);

			$page->set ('statuses', $statuses);
			$page->set ('formAction', self::getUrl ('page=company&id='.$objCompany->getId ().'&action=userman'));
		}

		$users = $objCompany->getUsers ();

		foreach ($users as $v)
		{
			$page->addListValue
			(
				'users',
				array
				(
					Core_Tools::output_varchar ($v[0]->getFullName ()),
					$text->get ($v[1], 'userstatus', 'company', $v[1]),
					$v[1],
					'user'.$v[0]->getId ()
				)
			);
		}

		$page->set ('noUsers', $text->get ('noUsers'));
		$page->set ('submit', $text->get ('submit'));

		return $page->parse ('company_user.tpl');
	}
	
	public function getPoefboekManagement ($objCompany)
	{
		$db = Core_Database::__getInstance ();
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('company');
		$text->setSection ('poefboek');

		$page = new Core_Template ();

		$page->set 
		(
			'title', 
			Core_Tools::putIntoText 
			(
				$text->get ('title'), 
				array (Core_Tools::output_varchar ($objCompany->getName ()))
			)
		);
		
		$page->set ('about', $text->get ('about'));

		$myself = Profile_Member::getMyself ();
		$status = $objCompany->getUserStatus ($myself);
		if ($myself && ($status == 'administrator' || $status == 'moderator'))
		{

			// Start with processing the input (since you're allowed to any way ;-))
			$count = 0;
			foreach ($_POST as $k => $v)
			{
				if (is_numeric ($v) && abs ($v) > 0)
				{
					if (substr ($k, 0, 4) == 'user')
					{
						$member = Profile_Member::getMember (substr ($k, 4));
						if ($member->isFound ())
						{
							$objCompany->addToMemberPoefboek ($member, $v);
							$count ++;
						}
					}
				}
			}

			if ($count > 0)
			{
				//header ('Location: '.self::getUrl ('page=company&id='.$objCompany->getId ()));
			}
		
			$page->set ('formAction', self::getUrl ('page=company&id='.$objCompany->getId ().'&action=poefboek'));
		}

		$users = $objCompany->getUsers ();

		foreach ($users as $v)
		{
			$page->addListValue
			(
				'users',
				array
				(
					Core_Tools::output_varchar ($v[0]->getFullName ()),
					Core_Tools::convert_price ($v[2]),
					'user'.$v[0]->getId ()
				)
			);
		}

		$page->set ('noUsers', $text->get ('noUsers'));
		$page->set ('submit', $text->get ('submit'));

		return $page->parse ('company_poef.tpl');
	}
	
	public function getPoefboekLog ($objCompany)
	{
		$me = Profile_Member::getMyself ();
		$status = $objCompany->getUserStatus ($me);
		
		if ($status != 'pending')
		{
			$user = Profile_Member::getMember (Core_Tools::getInput ('_GET', 'uid', 'int'));
			if ($user->isFound ())
			{
				$text = Core_Text::__getInstance ();
				
				$text->setFile ('company');
				$text->setSection ('poeflog');
				
				$page = new Core_Template ();
				
				$locname = Core_Tools::output_varchar ($user->getUsername ()) . 
					' @ ' . Core_Tools::output_varchar ($objCompany->getName ());
					
				$page->set ('poeflog', $text->get ('poeflog').': '.$locname);
				$page->set ('nologs', $text->get ('nologs'));
				
				$page->set ('return', $text->get ('return'));
				$page->set ('return_url', self::getUrl ('page=company&id='.$objCompany->getId ()));
				
				$page->set ('datum', $text->get ('datum'));
				$page->set ('amount', $text->get ('amount'));
				$page->set ('balance', $text->get ('balance'));
				$page->set ('actor', $text->get ('actor'));
				
				foreach ($objCompany->getPoefboekLog ($user) as $v)
				{
					$page->addListValue
					(
						'logs',
						array
						(
							'date' => date (DATETIME, $v['date']),
							'amount' => Core_Tools::convert_price ($v['amount']),
							'newpoef' => Core_Tools::convert_price ($v['newpoef']),
							'actor_name' => $v['actor_name'],
							'actor_url' => $v['actor_url']
						)
					);
				}
				
				return $page->parse ('company_poeflog.tpl');
			}
			else
			{
				return '<p>User not found.</p>';
			}
		}
		else
		{
			return '<p>No permission to watch logs.</p>';
		}
	}
}
?>