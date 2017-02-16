<?php

class PriceFinderVivat implements IPriceFinder {
	public $login = '';
	public $passwd = '';

	private $ar = null;
    private $priceCorr = null;
    private $weightCorr = null;

    public function __construct(array $values = [])
    {
        if ($values != null) {
            foreach ($values as $key=>$value) {
                $this->$key=$value;
            }
        }
        $rtUser = new RuntimeUser();
        $this->priceCorr = new PriceBuild($rtUser);
        $this->weightCorr = new WeightBuild($rtUser);
    }

	private function getDealerId()
	{
		return 2; // mean VIVAT
	}
	
	private function loadAR()
	{
		if (!$this->ar) {
            $this->ar = DbWebService::model()->findByPk(2); // 2 - Mean VIVAT
        }
	}
	
	private function responseParsed($data) {
        $list = new CList();
		foreach ($data->row as $item) {
            $list->add(
                new PriceItem([
                    '_dealer'=>$this->getDealerId(),
                    '_vendor'=>(string)$item->brand,
                    '_number'=>(int)$item->issubst == 0 ? (string)$item->partno : '',
                    '_replaceNumber'=>$item->issubst > 0 ? (string)$item->partno : '',
                    '_depot'=>(string)$item->depot,
                    '_desc_en'=>(string)$item->desceng,
                    '_desc_ru'=>(string)$item->descrus,
                    '_volume'=>(float)$item->volume,
                    '_weight'=>(float)$this->weightCorr->weight($item->weight),
                    '_price'=>(float)$this->priceCorr->price((float)$item->price),
                    '_qty'=>(int)$item->qty,
                ])
            );
        }
        return $list;
	}
	
	public function loadTokenFromDb()
	{
		$this->loadAR();
		return $this->ar->getToken();
	}
	
	public function saveTokenToDb($token)
	{
		$this->loadAR();
		$this->ar->setToken($token)->save();
	}
	
	public function getNewTokenFromVivat()
	{
		$this->loadAR();
		$pathMain=$this->ar->getPathMain();
		$pathToken=$this->ar->getPathToken();
		$path=$pathMain.$pathToken;
		$path=str_replace('$login',$this->login,$path);
		$path=str_replace('$passwd',$this->passwd,$path);
//		new DumpExit($path);
		$xml = new SimpleXMLElement(
		    @file_get_contents($path)
        );
        $token = (string)$xml->data->row->token;
        $ret = (int)$xml->status->id;
		if ($ret == 0) {
			$this->saveTokenToDb($token);
			$ret = true;
		}
		return $ret;
	}
	
	public function queryToVivat($search = null)
	{
        $ret = false;
        if ($search) {
            $token = $this->loadTokenFromDb();
            $pathMain=$this->ar->getPathMain();
            $pathPrice=$this->ar->getPathPrice();
            $path=$pathMain.$pathPrice;
            $path=str_replace('$token',$token,$path);
            $path=str_replace('$partno',$search,$path);
            if($content = @file_get_contents($path)) {
                $ret = new SimpleXMLElement($content);
            }
        }
		return $ret;
	}
	
    public function search2($numberToSearch)
    {
        $data = new CList();
        $errors = new CList();
        if (!($xml=$this->queryToVivat($numberToSearch))) {
            $errors->add("server no response");
        }
        else {
            // здесь мы предполагаем что токен просто устарел, не обрабатывая остальные ошибки !!!!
            if ($xml->status->id != null) {
                // и просто получаем новый токен
                $this->getNewTokenFromVivat();
                // и думаем, что все ОК
                $xml = $this->queryToVivat($numberToSearch);
                //new DumpExit($xml);
            }
            if (($xml->status->id == 0)&&($xml->status->text=='НЕТ В БАЗЕ ДАННЫХ')) {
                $errors->add("no in database");
            }
            elseif (($xml->status->id == 0)&&($xml->status->text=='НЕТ ЦЕНЫ')) {
                $errors->add("no price");
            }
            elseif ($xml->data->row->price == 0) {
                $errors->add("no price");
            }
            else {
                $data = $this->responseParsed($xml->data); //point to xml section <data>
            }
        }
        return new SearchResults($data, $errors);
    }
}