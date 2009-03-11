<?php
class Pages_Welcome extends Pages_Page
{
	private $sPage;
	
	public function __construct ($page)
	{
		$this->sPage = $page;
	}
	
	protected function getContent ()
	{
		$action = Core_Tools::getInput ('_POST', 'welcome_selection', 'varchar');
		
		switch ($action)
		{
			case 'honger':
				header ('Location: '.$this->getUrl ('page=register&nocompany=false'));
			break;
			
			case 'geld':
				header ('Location: '.$this->getUrl ('page=register&nocompany=true&action=shopowner'));
			break;
		}
	
		$text = Core_Text::__getInstance ();
		$text->setFile ('about');
		$text->setSection( 'home');
		
		$page = new Core_Template ();
		$page->set ('action', $this->getUrl ('page=welcome'));
		return $page->parse ('welcome.tpl');
	}
}
?>
