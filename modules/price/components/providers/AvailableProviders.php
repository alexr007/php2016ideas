<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AvailableProvidersList
 *
 * @author alexr
 */
class AvailableProviders implements IAvailableProviders {

	private $providers;
	
    public function __construct()
	{
		$this->providers = new CList();
		$this->buildProvidersList();
	}
	
	public function items()
	{
		return $this->providers;
	}

    private function buildProvidersList()
    {
        $vivat_access_data = [ 
			'norm' => [
				'login'=>'avtomirws',
				'passwd'=>'1d2c8h8a',
				],
			'dev' => [
				'login'=>'avtomirws_dev',
				'passwd'=>'4e7c6f5b',
				]
        ];
                
		$amt_access = ['ws_login'=>'KV','ws_passwd'=>'2016honda'];
		
        // select proper access mode according to dev/production mode
		$vivat_access = (Yii::app()->params->dev) 
			? $vivat_access_data['dev'] 
			: $vivat_access_data['norm'];
		
		$avail =  [
			PriceFinderConfiguration::configuration('amt', 'PriceFinderAMT', $amt_access),
			PriceFinderConfiguration::configuration('vivat', 'PriceFinderVivat', $vivat_access),
		];
		
		foreach ($avail as $api)
			$this->providers->add( new $api['class']($api['access']));
    }
}
