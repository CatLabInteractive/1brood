<?php
class Pages_About extends Pages_Page
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
		
		$page->set ('title', $text->get ('title'));
		
		$page->set ('about1', $text->get ('about1'));
		$page->set ('about2', $text->get ('about2'));

		$page->set ('forWho', $text->get ('forWho'));
		$page->set ('forWho1', $text->get ('forWho1'));

		$page->set ('cost', $text->get ('cost'));
		$page->set ('cost1', $text->get ('cost1'));

		$page->set ('examp', $text->get ('examp'));
		$page->set ('examp1', $text->get ('examp1'));
		$page->set ('examp2', $text->get ('examp2'));
		$page->set ('examp3', $text->get ('examp3'));
		
		return $page->parse ('about.tpl');
	}
}
?>
