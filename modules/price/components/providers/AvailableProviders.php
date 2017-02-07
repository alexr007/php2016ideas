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
        $accessVIVATdata = require(
            Yii::getPathOfAlias("application.config.access").DIRECTORY_SEPARATOR.'access_vivat.php'
        );

		$accessAMT = require(
            Yii::getPathOfAlias("application.config.access").DIRECTORY_SEPARATOR.'access_amt.php'
        );

		$accessVIVAT = (Yii::app()->params->dev)
			? $accessVIVATdata['dev']
			: $accessVIVATdata['norm'];
		
		$avail =  [
			PriceFinderConfiguration::configuration('amt', 'PriceFinderAMT', $accessAMT),
			PriceFinderConfiguration::configuration('vivat', 'PriceFinderVivat', $accessVIVAT),
		];
		
		foreach ($avail as $api)
			$this->providers->add( new $api['class']($api['access']));
    }
}
