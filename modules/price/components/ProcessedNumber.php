<?php

class ProcessedNumber {
	
	private $partNumber;
	private $providers;
	
	private $dataLink;
	private $errLink;
	
	public function __construct(/*PartNumber*/ $partNumber, IAvailableProviders $providers, CList &$data, CList &$err)
	{
		$this->partNumber = $partNumber;
		$this->providers = $providers;
		$this->dataLink = $data;
		$this->errLink = $err;
		
		$this->process();
		
		// если есть ошибки - то печатаем
		//foreach ($this->errLink->toArray() as $err)
		//	VarDumper::dump($err);
	}
	
	private function process()
	{
		new PriceLogger($this->partNumber, new DbPartSearchHistory());
		
		foreach ($this->providers->items() as $provider) { // цикл по провайдерам
			$this->processProvider($provider);
		}
	}
	
	private function processProvider($provider)
	{
		if ($provider->search($this->partNumber)) {
			$this->dataLink->mergeWith($provider->getResult());
		}
		else {
			$this->errLink->mergeWith($provider->getErrors());
		}
	}
	
}
