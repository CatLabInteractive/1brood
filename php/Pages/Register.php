<?php
class Pages_Register extends Pages_Page
{
	private $sPage;
	
	public function __construct ($page)
	{
		$this->sPage = $page;
	}
	
	protected function getContent ()
	{
		$myself = Profile_Member::getMyself ();

		$action = Core_Tools::getInput ('_GET', 'action', 'varchar', false);

		if (!$myself)
		{
			return $this->getRegistrationForm ();
		}

		elseif ($action == 'companies' || $myself->getRegStatus () == 1)
		{
			return $this->getChooseCompany ();
		}
		else
		{
			return $this->getOverview ();
		}
	}

	private function getRegistrationForm ()
	{
		$db = Core_Database::__getInstance ();

		$login = Core_Login::__getInstance ();
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection( 'register');

		// Check for input
		$firstname = Core_Tools::getInput ('_POST', 'firstname', 'varchar');
		$name = Core_Tools::getInput ('_POST', 'name', 'varchar');
		$email = Core_Tools::getInput ('_POST', 'email', 'varchar');
		$pass1 = Core_Tools::getInput ('_POST', 'password1', 'varchar');
		$pass2 = Core_Tools::getInput ('_POST', 'password2', 'varchar');

		$showform = true;

		if ($firstname && $name && $email && $pass1 && $pass2)
		{
			// Second check: E-mail
			$chk = $db->select
			(
				'players',
				array ('plid'),
				"email = '".$db->escape ($email)."'"
			);

			if (count ($chk) == 1)
			{
				$warning = $text->get ('emailFound');
			}
			else
			{
				// Seems to be alright... let's continue the quest.
				if ($pass1 != $pass2)
				{
					$warning = $text->get ('passFault');
				}
				else
				{
					$login->registerAccount ($firstname, $email, $pass1, $firstname, $name);
					$showform = false;

					if ($login->isLogin ())
					{
						// Post a redirect, just to be sure ;-)
						header ('Location: '.Pages_Page::getUrl ('page=register'));
						return $this->getChooseCompany ();
					}
					else
					{
						return '<p>'.$text->get ('done').'</p>';
					}
				}
			}
			
		}
		elseif ($firstname || $name || $email || $pass1 || $pass2)
		{
			$warning = $text->get ('complete');
		}

		if ($showform)
		{
			$page = new Core_Template ();

			$page->set ('form_action', self::getUrl ('page=register'));

			$page->set ('title', $text->get ('title'));
			$page->set ('about', $text->get ('about'));
			$page->set ('contactDetails', $text->get ('contactDetails'));

			if (isset ($warning))
			{
				$page->set ('warning', $warning);
			}

			// Form fields
			$page->set ('name', $text->get ('name'));
			$page->set ('firstname', $text->get ('firstname'));
			$page->set ('email', $text->get ('email'));
			$page->set ('password1', $text->get ('password1'));
			$page->set ('password2', $text->get ('password2'));
			$page->set ('submit', $text->get ('submit'));

			// Form field values
			$page->set ('name_value', Core_Tools::getInput ('_POST', 'name', 'varchar', null));
			$page->set ('firstname_value', Core_Tools::getInput ('_POST', 'firstname', 'varchar', null));
			$page->set ('email_value', Core_Tools::getInput ('_POST', 'email', 'varchar', null));
			
			return $page->parse ('register.tpl');
		}
	}

	private function getChooseCompany ()
	{	
		$text = Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('company');

		$myself = Profile_Member::getMyself ();

		$showForm = true;

		$companyId = Core_Tools::getInput ('_POST', 'chooseCompany', 'int');
		if ($companyId)
		{
			$company = Profile_Company::getCompany ($companyId);
			if ($company->isFound ())
			{
				$company->addMember ($myself);
				$showForm = false;

				header ('Location: '.self::getUrl ('page=register'));

				return '<p>'.$text->get ('done').'</p>';
			}
		}

		if ($showForm)
		{
			$page = new Core_Template ();

			$page->set ('title', $text->get ('title'));
			$page->set ('about', $text->get ('about'));
			$page->set ('choose', $text->get ('choose'));
			
			$page->set ('noCompanies', $text->get ('noCompanies'));
			$page->set ('add', $text->getClickTo ($text->get ('toAddCompanie')));
			$page->set ('addUrl', self::getUrl ('page=company&action=add'));

			$companies = Profile_Company::getCompanies ();
			foreach ($companies as $v)
			{
				$page->addListValue
				(
					'companies',
					array
					(
						$v->getName (),
						$v->getId ()
					)
				);
			}

			$page->set ('chooseComp', $text->get ('chooseComp'));
			$page->set ('submit', $text->get ('submit'));
			$page->set ('formAction', self::getUrl ('page=register&action=companies'));

			return $page->parse ('account_comp.tpl');
		}
	}

	private function getOverview ()
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('account');
		$text->setSection ('overview');
	
		$page = new Core_Template ();

		$page->set ('title', $text->get ('title'));
		$page->set ('about', $text->get ('about'));
		$page->set ('companies', $text->get ('companies'));
		$page->set ('compAbout', $text->get ('compAbout'));
		$page->set ('noCompanies', $text->get ('noCompanies'));
		$page->set ('pending', $text->get ('pending'));
		$page->set ('aboutPending', $text->get ('aboutPending'));
		
		$page->set ('poeflog', $text->get ('poeflog'));

		$page->set ('addCompany', $text->getClickTo ($text->get ('toAddCompanie')));
		$page->set ('addCompanyUrl', self::getUrl ('page=register&action=companies'));

		$myself = Profile_Member::getMyself ();

		$companies = $myself->getMyCompanies ();
		foreach ($companies as $v)
		{
			$page->addListValue
			(
				'companies',
				array
				(
					Core_Tools::output_varchar ($v->getName ()),
					self::getUrl ('page=company&id='.$v->getId ()),
					self::getUrl ('page=company&id='.$v->getId ().'&action=poeflog&uid='.$myself->getId ())
				)
			);
		}

		$pending = $myself->getMyCompanies (true);
		foreach ($pending as $v)
		{
			$page->addListValue
			(
				'pending',
				array
				(
					Core_Tools::output_varchar ($v->getName ()),
					self::getUrl ('page=company&id='.$v->getId ())
				)
			);
		}

		return $page->parse ('account_overview.tpl');
	}
}
?>