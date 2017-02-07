<?php

class FlagCss {
	
	private $dealerid;
	
	public function __construct($dealerid)
	{
		$this->dealerid = $dealerid;
	}
	
	public function value()
	{
		switch ((int)$this->dealerid)
		{
			case 1 : return "flag-usa";
			case 2 : return "flag-uae";
			case 3 : return "flag-ukr";
		}
		
	}
}
