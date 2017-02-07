<?php

class ProcessFilteredNumbers {
	
	private $cleanedNumbers;
	private $providers;
	
	public function __construct(CleanedNumbers $cleanedNumbers, IAvailableProviders $providers)
	{
		$this->cleanedNumbers = $cleanedNumbers;
		$this->providers = $providers;
	}
	
	function process()
	{
		if ($this->cleanedNumbers->value()->count() == 0) return;
		
		$data = new CList();
		$err = new CList(); // ошибки, возможно, потом обработаем
		
		foreach ($this->cleanedNumbers->value() as $number) 
			new ProcessedNumber($number, $this->providers, $data, $err);

		new FoundResultsStore($data, new RuntimeSession());
	}
	
}
