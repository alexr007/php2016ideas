<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PriceFinderConfiguration
 *
 * @author alexr
 */
class PriceFinderConfiguration {
	
	public static function configuration($alias = null, $class = null, $access = null)
	{
		return [
			'name'=>$alias,
			'class'=>$class,
			'access'=>$access,
		];

	}
}
