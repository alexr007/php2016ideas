<?php

/**
 * Created by PhpStorm.
 * User: alexr
 * Date: 07.03.2017
 * Time: 12:35
 */
class InvoiceLine
{
    private $vendor;
    private $number;
    private $qty;

    public function __construct($vendor, $number, $qty) {
        $this->vendor = (new NormalizedVendor($vendor))->get();
        $this->number = (new NormalizedPartNumber($number))->get();
        $this->qty = (new NormalizedQty($qty))->get();
    }

    /**
     * @return mixed
     */
    public function vendor()
    {
        return $this->vendor;
    }

    /**
     * @return mixed
     */
    public function number()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function qty()
    {
        return $this->qty;
    }

    public function toString() {
        return "V:".$this->vendor().
            " N:".$this->number().
            " Q:".$this->qty();
    }
}