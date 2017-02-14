<?php

class PriceFinderSQLTable extends PriceFinder
{
	private $items;
	
	protected function getDealerId() //flag
	{
		return 4;
	}
	
	private function fillResult()
	{
	    $eur = new ExchangeEURUSD();
		// цикл по массиву который нам отдал SOAP
		foreach ($this->items as $item) {
			//new DumpExit($item);
		    $this->addResult(
				new PriceItem([
					'_dealer'=>$this->getDealerId(),
                    '_vendor'=>$item->prVendor->name(),
					'_number'=>$item->pr_number,
					//'_replaceNumber'=>$this->parseReplace($item['replace']),
					'_desc_en'=>$item->pr_name,
					//'_desc_ru'=> mb_substr($item['descr_ru'], 0, self::MAX_LENGTH)  ,
                    '_weight'=>(float)$this->weightModify($item->pr_weight),
					'_price'=>$this->priceModify($eur->value($item->pr_price+$item->pr_core)),
					'_core'=>$item->pr_core,
				])
			);
        }
	}

	public function searchItem()
	{
	    // $this->getSearch() - то что ищем
        $this->items = DbPrice::model()
            ->with('prVendor')
            ->findAllByAttributes(['pr_number'=>$this->getSearch()]);
        //new DumpExit($this->items);
		// заполняем $this->result
		$this->fillResult();
		return true;
	}
}