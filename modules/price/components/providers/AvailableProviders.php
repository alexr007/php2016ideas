<?php

class AvailableProviders implements IAvailableProviders {

	public function items()
	{
        $accessVIVATdata = require(
            Yii::getPathOfAlias("application.config.access").DIRECTORY_SEPARATOR.'access_vivat.php'
        );
        $accessVIVAT = (Yii::app()->params->dev)
            ? $accessVIVATdata['dev']
            : $accessVIVATdata['norm'];
        $accessAMT = require(
            Yii::getPathOfAlias("application.config.access").DIRECTORY_SEPARATOR.'access_amt.php'
        );

        $avail =  [
            new PriceFinderConfiguration('amt', 'PriceFinderAMT', $accessAMT),
            new PriceFinderConfiguration('vivat', 'PriceFinderVivat', $accessVIVAT),
            new PriceFinderConfiguration('germany1', 'PriceFinderSQLTable', []),
        ];

        $providers = new CList();
        foreach ($avail as $item) {
            $className = $item->className();
            $providers->add(
                new $className($item->access())
            );
        }
		return $providers;
	}
}
