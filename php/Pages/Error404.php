<?php
class Pages_Error404 extends Pages_Page
{
	private $sPage;
	
	public function __construct ($page)
	{
		$this->sPage = $page;
	}
	
	protected function getContent ()
	{
		$page = new Core_Template ();
		
		$text = Core_Text::__getInstance ();
		
		$page->set ('title', Core_Tools::putIntoText ($text->get ('title', 'error404', 'main'), array ($this->sPage)));
		$page->set ('descr', $text->get ('descr', 'error404', 'main'));
		
		return $page->parse ('error404.tpl');
	}
}
?>