<?php

/**
 * Created by PhpStorm.
 * User: alexr
 * Date: 09.03.2017
 * Time: 23:34
 */
class Invoice
{
    private $list;

    public function __construct() {
        $this->list = new JList();
    }

    public function add(InvoiceLine $line) {
        $index = $this->indexOf($line->pn());
        if ($index === false) {
            $this->list->add($line);
        }
        else {
            $this->list->itemAt($index)->incQty($line->qty());
        }
    }

    private function indexOf(InvPartNumber $item) {
        $ret = false;
        foreach ($this->list as $k=>$listItem) {
            if ($listItem->pn()->equals($item)) {
                $ret = $k;
                break;
            }
        }
        return $ret;
    }

    public function contains(InvPartNumber $item) {
        return $this->indexOf($item) !== false;
    }

    public function remove(InvPartNumber $item) {
        $index = $this->indexOf($item);
        if ($index === false) {
            return false;
        }
        else {
            $this->list->removeAt($index);
            return true;
        }
    }

    public function toString() {
        return $this->list->toString();
    }
}