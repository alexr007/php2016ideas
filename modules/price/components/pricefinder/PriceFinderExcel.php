<?php

class PriceFinderNotusedExcel extends PriceFinder_not_used
{
	private $file;
	private $items;
	
	public function __construct($file, $values = array()) {
		parent::__construct($values);
		$this->file = $file;
		$this->items = new CList();

	}
	
	protected function excelConnect()
	{
		$this->_connect->add( PHPExcel_IOFactory::load($this->file)	);
	}
	
	protected function excelConnected()
	{
		return $this->_connect->count() != 0;
	}
	
	protected function excelConnection()
	{
		if (!$this->excelConnected())
			$this->excelConnect();
		
		return $this->_connect->itemAt(0);
	}
	
	protected function getDealerId()
	{
		return 3;
	}
	
	private function fillResult()
	{
		// цикл по массиву который нам отдал поиск по файлу
		foreach ($this->items as $item)
			$this->addResult(
				new PriceItem([
					'_dealer'=>$this->getDealerId(),
					'_vendor'=>$item['A'],
					'_number'=>$item['B'],
					'_price'=>$this->priceModify($item['C']),
				])
			);
	}
	
	public function searchItem()
	{
		$this->items->clear();
		$sheet = $this->excelConnection()->getActiveSheet()->toArray(null,true,true,true);
		
		foreach ($sheet as $row)
			foreach ($row as $cell)
				if (!(mb_strpos(mb_strtoupper((string)$cell), $this->getSearch())=== false))
					$this->items->add($row);
		
		$this->fillResult();
		return true;
	}
}