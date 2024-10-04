<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Codexshaper\WooCommerce\Facades\Product as WooProduk;
use Codexshaper\WooCommerce\Facades\Coupon;
use Codexshaper\WooCommerce\Facades\Customer;
use Codexshaper\WooCommerce\Facades\WooCommerce as FacadesWooCommerce;
use Automattic\WooCommerce\Client;
use Codexshaper\WooCommerce\Facades\Webhook;
class WoocommerceModel extends Model
{
    use HasFactory;
    private $WOOCOMMERCE_STORE_URL;
    private $WOOCOMMERCE_CONSUMER_KEY;
    private $WOOCOMMERCE_CONSUMER_SECRET;

    public function __construct() {
        $this->WOOCOMMERCE_STORE_URL = 'https://jakartacamera.com';
        $this->WOOCOMMERCE_CONSUMER_KEY = 'ck_c46c613c61a7afda407d20f59593c4cc1523d22e';
        $this->WOOCOMMERCE_CONSUMER_SECRET = 'cs_6aa1f62f6dd3539d47ac1cb528d954f276b60352';
    }

    function authWooCommerce($data) {
        $local_store = $data['local_store'];
        $remote_store = $data['remote_store'];

        $store_url = 'https://jakartacamera.com';
        $endpoint = '/wc-auth/v1/authorize';
        $params = [
            'app_name' => 'Koneksi Ginee',
            'scope' => 'read_write',
            'user_id' => 1,
            'return_url' => 'http://jakartacamera.com',
            'callback_url' => 'https://new.jakartacamera.com'
        ];
        $query_string = http_build_query( $params );

        return $store_url . $endpoint . '?' . $query_string;
    }

    public function getCategories($params = []){
        $paramsString = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->WOOCOMMERCE_STORE_URL."/wp-json/wc/v3/products/categories?". $paramsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->WOOCOMMERCE_CONSUMER_KEY . ":" . $this->WOOCOMMERCE_CONSUMER_SECRET);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }

    public function getProduk($params = []){
        $paramsString = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->WOOCOMMERCE_STORE_URL."/wp-json/wc/v3/products?". $paramsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->WOOCOMMERCE_CONSUMER_KEY . ":" . $this->WOOCOMMERCE_CONSUMER_SECRET);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }

    public function webhookCreate() {
        $data = [
            'name' => 'Order updated',
            'topic' => 'order.updated',
            'delivery_url' => ''
        ];

        return Webhook::create($data);
    }

    public function webhookFind($id) {
        return Webhook::find($id);
    }

    public function webhookFindAll() {
        return Webhook::all();
    }

    public function webhookUpdate($id, ) {
        $data = [
            'status' => 'paused'
        ];

        return Webhook::update($id, $data);
    }

    public function webhookDelete($id) {
        $options = ['force' => true]; // Set force option true for delete permanently. Default value false

        return Webhook::delete($id, $options);
    }

    public function webhookBatch($id) {
        $data = [
            'create' => [
                [
                    'name' => 'Round toe',
                    'topic' => 'coupon.created',
                    'delivery_url' => ''
                ],
                [
                    'name' => 'Customer deleted',
                    'topic' => 'customer.deleted',
                    'delivery_url' => ''
                ]
            ],
            'update' => [
                'status' => 'paused'
            ],
            'delete' => [
                $id
            ]
        ];

        return Webhook::batch($data);
    }

}
