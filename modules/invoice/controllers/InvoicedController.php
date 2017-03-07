<?php

class InvoicedController extends InvoiceController
{
	public $defaultAction = 'parse';

    public function actionParse()
	{
        $model = new FormInvoice();
        if (isset($_POST['FormInvoice'])) {
            $model->attributes = $_POST['FormInvoice'];
            $this->render('invoice_parsed',[
                'processed' => new InvoiceProcessed($model),
            ]);
        }
        else {
            $this->render('invoice_getinfo',[
                'model' => $model,
                'dealers' => new DbDealer(),
            ]);
        }

	}

}