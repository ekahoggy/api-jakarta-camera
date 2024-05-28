<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class Xendit extends Model
{
    use HasFactory;

    public function __construct()
    {
        Configuration::setXenditKey("xnd_development_HQd5bgrmv9rogMmufef5gxZKdatydoMzI5hL3gHbyMB0U2hR19e9sUy60VsF6k");
    }

    public function createInvoice($data)
    {
        $apiInstance = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => $data['invoice_number'],
            'description' => isset($data['description']) ? $data['description'] : '-',
            'amount' => $data['grand_total'],
            'currency' => 'IDR',
            'reminder_time' => 1
        ]);

        try {
            $result = $apiInstance->createInvoice($create_invoice_request);
            return $result;
        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }

    public function webhook($data)
    {

    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
