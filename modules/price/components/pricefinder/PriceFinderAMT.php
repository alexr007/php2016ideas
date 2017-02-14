<?php

class PriceFinderAMT extends PriceFinder
{
	public $ws_login = '';
	public $ws_passwd = '';
	public $ws_params = ['encoding'=>'cp1251','connection_timeout'=>3, 'exceptions'=>0]; //,'trace'=>1
	public $ws_path = "http://automototrade.com/wsdl/server.php?wsdl";

	private $item;
	private $items;
	
	const MAX_LENGTH=50;
	
	protected function getDealerId() //flag
	{
		return 1;
	}
	
	private function checkVendor($vendor = null)
	{
        if(!preg_match("/^[0-9]+$/", $vendor)) 
			$this->addError("vendor id invalid");

    	if (+($vendor) > 9999 ) 
			$this->addError("vendor id invalid");
	}
	
	private function parseReplace($data = null)
	{
		if (!$data) return false;
		/* 
		 * special handling for AMT trash response like this:
		 * 'Part number 4884899AB was superceded by part number 4884899AC.'
		 */
		$lastSpacePos = strrpos($data, " ");
		return substr($data, $lastSpacePos, -1);
	}
	
	private function fillResult()
	{
		// цикл по массиву который нам отдал SOAP
		foreach ($this->items as $item)
			$this->addResult(
				new PriceItem([
					//'_dealer'='>'USA',
					'_dealer'=>$this->getDealerId(),
					'_vendor'=>$item['brand'],
					'_number'=>$item['oem_original'],
					'_replaceNumber'=>$this->parseReplace($item['replace']),
					'_desc_en'=>$item['descr'],
					'_desc_ru'=> mb_substr($item['descr_ru'], 0, self::MAX_LENGTH)  ,
					//'_weight'=>$item['weight'],
                    '_weight'=>(float)$this->weightModify($item['weight']),
					'_price'=>$this->priceModify($item['list_price']),
					'_core'=>$item['core_price'],
				])
			);
	}
					
	protected function SOAPconnect()
	{
		$this->_connect->add( new SoapClient($this->ws_path, $this->ws_params)	);
	}
	
	protected function SOAPconnected()
	{
		return $this->_connect->count() != 0;
	}
	
	protected function SoapConnection()
	{
		if (!$this->SOAPconnected())
			$this->SOAPconnect();
		
		return $this->_connect->itemAt(0);
	}
	
	private function SoapQuery($function, $params = [])
	{
		$access = [	'login'=>$this->ws_login,
					'passwd'=>$this->ws_passwd];
					
		/*
		new DumpExit($access,false);
		new DumpExit($params,false);
		new YiiFinish();
		 * 
		 */
		
		switch (count($params))	{
			case 0 : $result=$this->SoapConnection()->$function($access);break;
			case 1 : $result=$this->SoapConnection()->$function($params[0], $access);break;
			case 2 : $result=$this->SoapConnection()->$function($params[0],$params[1], $access);break;
			case 3 : $result=$this->SoapConnection()->$function($params[0],$params[1],$params[2], $access);break;
		}
		if (is_soap_fault($result)) {
			echo "SOAP error";
			return false;
		} else 
			return $result;
	}
	
	public function searchByVendor($search = [])
	{
		$this->clearErrors();

		if ($search == null) {
			$this->addError("wrong parameters given");
			return false;
		}
		
		$vendor = $search['vendor']; // id !
		$number = $search['number'];
		
		$this->checkNumber($number);
		$this->checkVendor($vendor);

		if ($this->hasErrors()) return false;
		
		$this->SoapConnect();
	 	
		// цикл по заменам
		while (true) {
	 		$this->item = $this->SoapQuery('getPartsPrice', [$number, $vendor]);	
			
			if (count($this->item)==1) {
				$this->addError($this->item[0]);
				return false;
			}
			
			if ($this->item['Replace'] == '') break;
				else $number = parseReplace($this->item['Replace']);
	 	} 
		
	 	// обрабатываем результат
		if ($this->item['PartNameEng'] == 'No Part Found') {
			$this->addError("No Part Found");
			return false;
	 	}
		
		$this->fillResult();
		return true;
	}
	
	public function searchItem()
	{
	 	$items = $this->SoapQuery('getPriceByOem', [
				[$this->getSearch()],
			]);
		
		if ((count($items)==1)&&($items['0']=='Incorrect parametrs...')) {
			$this->addError($items['0']);
			return false;
		}
		
		if ((count($items)==1)&&($items['0']['oem_original']==null)) {
			$this->addError('OEM Number not found');
			return false;
		}
		
		foreach ($items as &$item) 
			$item['descr_ru'] = iconv("Windows-1251","UTF-8",$item['descr_ru']);
		
		$this->items = $items;
		$this->fillResult();
		return true;
	}
}