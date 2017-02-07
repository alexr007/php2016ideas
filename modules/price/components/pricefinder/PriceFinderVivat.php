<?php

class PriceFinderVivat extends PriceFinder
{
	public $login = '';
	public $passwd = '';
	public $token = '';
	public $ws_path = '';

	private $items;
	private $_dbAr = null;
	
	protected function getDealerId()
	{
		return 2;
	}
	
	private function loadAr()
	{
		if ($this->_dbAr == null)
			$this->_dbAr = DbWebService::model()->findByPk(2); // 2 - Mean VIVAT
	}
	
	private function fillResult()
	{
		$data = $this->items->data; //point to xml section <data>
		
		foreach ($data->row as $item)
			$this->addResult(
				new PriceItem([
					//'_dealer'=>'UAE',
					'_dealer'=>$this->getDealerId(),
					'_vendor'=>(string)$item->brand,
					'_number'=>(int)$item->issubst == 0 ? (string)$item->partno : '',
					'_replaceNumber'=>$item->issubst > 0 ? (string)$item->partno : '',
					'_depot'=>(string)$item->depot,
					'_desc_en'=>(string)$item->desceng,
					'_desc_ru'=>(string)$item->descrus,
					'_volume'=>(float)$item->volume,
                    //'_weight'=>(float)$item->weight,
                    '_weight'=>(float)$this->weightModify($item->weight),
					'_price'=>$this->priceModify((float)$item->price),
					'_qty'=>(int)$item->qty,
				])
			);	
	}
	
	public function loadTokenFromDb()
	{
		$this->loadAr();
		$token = $this->_dbAr->getToken();
		return $token;
	}
	
	public function saveTokenToDb($token)
	{
		$this->loadAr();
		$this->_dbAr->setToken($token)->save();
	}
	
	public function getNewTokenFromVivat()
	{
		$this->loadAr();
		$pathMain=$this->_dbAr->getPathMain();
		$pathToken=$this->_dbAr->getPathToken();
		$path=$pathMain.$pathToken;
		$path=str_replace('$login',$this->login,$path);
		$path=str_replace('$passwd',$this->passwd,$path);
		
//		new DumpExit($path);
		
		$content = file_get_contents($path);
		$xml = new SimpleXMLElement($content);
		
		$status_id = (int)$xml->status->id;
		$status_text =(string)$xml->status->text;
		$token = (string)$xml->data->row->token;

		if ($status_id == null) {
			$this->saveTokenToDb($token);
			return true;
		}
		
		return $status_id;
	}
	
	public function queryToVivat($search = null)
	{
		if ($search == null) return false;
		
		$token = $this->loadTokenFromDb();
		$pathMain=$this->_dbAr->getPathMain();
		$pathPrice=$this->_dbAr->getPathPrice();
		$path=$pathMain.$pathPrice;
		$path=str_replace('$token',$token,$path);
		$path=str_replace('$partno',$search,$path);
		
		if(!($content = @file_get_contents($path))) return false;
		
		return new SimpleXMLElement($content);
	}
	
	public function searchItem()
	{
		$number = $this->getSearch();
		$xml = $this->queryToVivat($number);
		//new DumpExit($xml, false);
		
		if (!$xml) {
			$this->addError("server no response");
			return false;
		}
		
		// здесь мы предполагаем что токен просто устарел, не обрабатывая остальные ошибки !!!!
		if ($xml->status->id != null) {
			// и просто получаем новый токен и думаем, что все ОК
			$this->getNewTokenFromVivat();
			$xml = $this->queryToVivat($number);
		}
		
		//new DumpExit($xml);
		
		if (($xml->status->id == 0)&&($xml->status->text=='НЕТ В БАЗЕ ДАННЫХ')) {
			$this->addError("not found");
			return false;
		}
		
		if ($xml->data->row->price == 0) {
			$this->addError("not found");
			return false;
		}
		
		$this->items = $xml;
		
		$this->fillResult();
		return true;
	}
}