<?php
class Pages_Donate extends Pages_Page
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
		$text->setFile ('about');
		$text->setSection ('about');
		
		
		
		return $page->parse ('donate.tpl');
	}
}
?>
