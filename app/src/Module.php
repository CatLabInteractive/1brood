<?php

/**
 * Class Module
 */
class Module implements \Neuron\Interfaces\Module
{

	/**
	 * Set template paths, config vars, etc
	 * @param string $routepath The prefix that should be added to all route paths.
	 * @return void
	 */
	public function initialize ($routepath)
	{
		require_once 'initialize.php';
	}

	/**
	 * Register the routes required for this module.
	 * @param \Neuron\Router $router
	 * @return void
	 */
	public function setRoutes (\Neuron\Router $router)
	{
		$router->get('/', 'Pages_Home@getHTML');
		$router->get('/about/{a?}/{b?}/{c?}/{d?}', 'Pages_About@getHTML');
		$router->match('POST|GET', '/home/{a?}/{b?}/{c?}/{d?}', 'Pages_Home@getHTML');
		$router->match('POST|GET', '/register/{a?}/{b?}/{c?}/{d?}', 'Pages_Register@getHTML');
		$router->match('POST|GET', '/welcome/{a?}/{b?}/{c?}/{d?}', 'Pages_Welcome@getHTML');
		$router->match('POST|GET', '/company/{a?}/{b?}/{c?}/{d?}', 'Pages_Company@getHTML');
		$router->match('POST|GET', '/donate/{a?}/{b?}/{c?}/{d?}', 'Pages_Donate@getHTML');
		$router->match('POST|GET', '/order/{a?}/{b?}/{c?}/{d?}', 'Pages_Order@getHTML');
		$router->match('POST|GET', '/shop/{a?}/{b?}/{c?}/{d?}', 'Pages_Shop@getHTML');
		$router->match('POST|GET', '/lostPassword/{a?}/{b?}/{c?}/{d?}', 'Pages_LostPassword@getHTML');
	}
}