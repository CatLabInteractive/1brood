<?php

class Core_Template
{

	private $values = array ();
	private $lists = array ();
	
	private $sTextFile = null;
	private $sTextSection = null;
	
	private $objText = null;
	
	public static function getUniqueId ()
	{
		if (!isset ($_SESSION['tc']))
		{
			$_SESSION['tc'] = time ();
		}
		
		$_SESSION['tc'] ++;
		
		return $_SESSION['tc'];
	}

	public static function getStylesheets ()
	{
		$style = array ();
		if (defined (TEMPLATE_DIR) && is_readable (TEMPLATE_DIR.'/style.php')) 
		{
			include TEMPLATE_DIR.'/style.php';
		}
		
		elseif (is_readable (DEFAULT_TEMPLATE_DIR.'/style.php')) 
		{
			include DEFAULT_TEMPLATE_DIR.'/style.php';
		}
		return $styles;
	}
	
	// Text function
	public function setTextSection ($sTextSection, $sTextFile = null)
	{
		$this->sTextSection = $sTextSection;
		
		if (isset ($sTextFile))
		{
			$this->sTextFile = $sTextFile;
		}
	}
	
	public function setTextFile ($sTextFile)
	{
		$this->sTextFile = $sTextFile;
	}

	public function set ($var, $value, $overwrite = false, $first = false)
	{
		$this->setVariable ($var, $value, $overwrite, $first);
	}
	
	// Intern function
	private function getText ($sKey, $sSection = null, $sFile = null, $sDefault = null)
	{
		if (!isset ($this->objText))
		{
			$this->objText = Core_Text::__getInstance ();
		}
		
		return $this->objText->get 
		(
			$sKey, 
			isset ($sSection) ? $sSection : $this->sTextSection, 
			isset ($sFile) ? $sFile : $this->sTextFile,
			$sDefault
		);
	}
	
	public function getClickTo ($sKey, $sSection = null, $sFile = null)
	{
		if (!isset ($this->objText))
		{
			$this->objText = Core_Text::__getInstance ();
		}
		
		return $this->objText->getClickTo ($this->getText ($sKey, $sSection, $sFile));
	}

	public function setVariable ($var, $value, $overwrite = false, $first = false)
	{
	
		if ($overwrite)
		{
		
			$this->values[$var] = $value;
		
		}
		
		else {
	
			if (isset ($this->values[$var]))
			{
		
				if ($first)
				{
				
					$this->values[$var] = $value.$this->values[$var];
				
				}
				
				else {
				
					$this->values[$var].= $value;
				
				}
			
			}
			
			else {
			
				$this->values[$var] = $value;
			
			}
		
		}
	
	}
	
	public function addListValue ($var, $value)
	{
	
		$this->lists[$var][] = $value;
	
	}
	
	public function sortList ($var)
	{
		if (isset ($this->lists[$var]))
		{
			sort ($this->lists[$var]);
		}
	}

	public function parse ($template)
	{
		/* Set static url adress */
		$this->set ('static_url', STATIC_URL.'/layout/templates/default/');
		$this->set ('template_name', substr (TEMPLATE_DIR, 10));

		foreach ($this->values as $k => $v)
		{
		
			$$k = $v;
		
		}
		
		foreach ($this->lists as $k => $v)
		{
		
			$n = 'list_'.$k;
			
			$$n = $v;
		
		}

		ob_start ();
		
		if (defined ('template_dir') && is_readable (TEMPLATE_DIR.'/'.$template)) {
		
			include TEMPLATE_DIR.'/'.$template;
		
		}
		
		elseif (is_readable (DEFAULT_TEMPLATE_DIR.'/'.$template)) {
		
			include DEFAULT_TEMPLATE_DIR.'/'.$template;
		
		}
		
		else {
		
			echo '<h1>Template not found</h1>';
			echo '<p>'.DEFAULT_TEMPLATE_DIR.'/'.$template.'</p>';
		
		}
		
		$val = ob_get_contents();
		ob_end_clean();
		
		return $val;
	}

}

?>
