<?php

/**
 * Created by PhpStorm.
 * User: alexr
 * Date: 08.02.2017
 * Time: 23:03
 */
class UploadPriceToDb
{
    private $file;

    public function __construct($file) {
        $this->file = $file;
    }

    private function stripQuotes($line) {
        return $line;
        return substr($line,1,strlen($line)-2);
    }

    public function upload() {
        $handle = fopen($this->file, "r");
        fgets($handle);
        while ($line = trim(fgets($handle))) {
            $items = explode(";", $line);
            new DumpExit($this->stripQuotes($items[0]), false);
            new DumpExit($this->stripQuotes($items[1]), false);
            new DumpExit($this->stripQuotes($items[2]), false);
            new DumpExit($this->stripQuotes($items[6]));
        }
    }
}