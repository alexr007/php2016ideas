<?php

class InvoicedController extends InvoiceController
{
	public $defaultAction = 'parse';

    public function actionParse()
	{
        $model = new FormInvoice();
        if (!isset($_POST['FormInvoice'])) {
            $this->render('invoice_getinfo',[
                'model' => $model,
                'dealers' => new DbDealer(),
                'methods' => new DbDeliveryType()
            ]);
        }
        else {
            $model->attributes = $_POST['FormInvoice'];

            // инвойс
            $inv = new Invoice($model->invoice,
                $model->dealer,
                $model->method,
                $model->date,
                // обрабатываем содержимое инвойса
                (new InvoiceParsed($model->details))->details()
            );

            // todo 1. читаем db.orders в коллекцию
            // todo 2. сравниваем $invoice И $db.orders
            // todo 3. пишем $db.orders в БД

            // сохраняем остаток коллекции $invoice
            $inv->storeToDb();

            $this->render('invoice_parsed',[
                'processed' => $inv->details(),
            ]);
        }

	}

}