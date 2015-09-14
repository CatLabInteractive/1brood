<?php
class Pages_Home extends Pages_Page
{
	private $sPage;
	
	public function __construct ($page)
	{
		$this->sPage = $page;
	}
	
	protected function getContent ()
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('about');
		$text->setSection( 'home');
		
		$page = new Core_Template ();

		$page->set ('title', $text->get ('title'));

		$page->set ('manual', $text->get ('manual'));
		
		$page->set ('companies', $text->get ('companies'));

		$page->set ('login', $text->get ('login'));
		$page->set ('choose', $text->get ('choose'));
		$page->set ('order', $text->get ('order'));
		$page->set ('eat', $text->get ('eat'));

		$page->set ('toMoreInfo', $text->getClickTo ($text->get ('toMoreInfo')));

		$page->set ('moreInfoLink', self::getUrl ('page=about'));
		
		return $page->parse ('home.tpl');
	}
}
?>
