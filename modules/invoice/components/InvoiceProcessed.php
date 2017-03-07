<?php
/**
 * Created by PhpStorm.
 * User: alexr
 * Date: 07.03.2017
 * Time: 13:31
 */
class InvoiceProcessed {
    private $model;

    public function __construct($data){
        $this->model = $data;
    }

    private function process() {
        foreach (explode("\n", $this->model->details) as $line) {
            $row = explode("\t", $line);
            if (count($row)==3) {
                $invoiceLine = new InvoiceLine($row[0],$row[1],$row[2]);
                new DumpExit($invoiceLine->toString(),false);
            }
        }
    }

    public function data() {
        $this->process();
        return "Done";
    }
}