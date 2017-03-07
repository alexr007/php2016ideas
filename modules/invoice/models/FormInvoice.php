<?php
/**
 * Created by PhpStorm.
 * User: alexr
 * Date: 07.03.2017
 * Time: 12:32
 */
class FormInvoice extends CFormModel
{
    public $date;
    public $dealer;
    public $invoice;
    public $details;

    public function rules()
    {
        return [
            ['date, dealer, invoice, details', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'date' => 'Enter Date',
            'dealer' => 'Select Dealer',
            'invoice' => 'Enter Invoice Number',
            'details' => 'Paste Invoice Details'
        ];
    }
}