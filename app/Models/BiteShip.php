<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Setting;

class BiteShip extends Model
{
    private $key = 'biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiQVBJIFRFU1RJTkciLCJ1c2VySWQiOiI2NjY4MTQ2OTQyZWJhZDAwMTNlYTJjMzAiLCJpYXQiOjE3MTgyNzE3ODl9.X78-4tuKDPLY1SLPsVh2vckouzbvQN2m8uNME3xk3r8';
    use HasFactory;

    public function getCourier(){
        $curl = curl_init();
        //area search accurate by kode pos
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.biteship.com/v1/couriers',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type:application/json',
            'Authorization:Bearer '. $this->key
            ),
        ));

        $result = curl_exec($curl);

        curl_close($curl);
        $err = curl_error($curl);

        $res = [];
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $res = json_decode($result, true);
        }

        $d1 = [];
        $newArr = [];
        foreach ($res['couriers'] as $key => $value) {
            $d1[$value['courier_code']][$key] = $value;
        }
        foreach ($d1 as $key => $value) {
            $newArr[$key]['code'] = $key;
            foreach ($value as $k => $v) {
                $newArr[$key]['name'] = $v['courier_name'];
                $newArr[$key]['service_available'][] = $v['courier_service_name'];
                $newArr[$key]['description'][] = $v['description'];
                $newArr[$key]['shipping_type'][] = $v['shipping_type'];
                $newArr[$key]['duration'][] = $v['shipment_duration_range'];
                $newArr[$key]['duration_type'][] = $v['shipment_duration_unit'];
                $newArr[$key]['tier'][] = $v['tier'];

                //true false
                $newArr[$key]['cod'][] = $v['available_for_cash_on_delivery'];
                $newArr[$key]['bukti_pengiriman'][] = $v['available_for_proof_of_delivery'];
                $newArr[$key]['instant_waybill'][] = $v['available_for_instant_waybill_id'];
            }
        }

        foreach ($newArr as $key => $value) {
            foreach ($value['duration'] as $index => $duration) {
                $newArr[$key]['new_durasi'][] = $duration . ' ' . $value['duration_type'][$index];
            }
        }

        return array_values($newArr);
    }

    public function getRates($params){
        $payload = json_encode($params);
        $ch = curl_init("https://api.biteship.com/v1/rates/couriers");
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:Bearer '. $this->key
        ));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        $err = curl_error($ch);

        $data = [];
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $data = json_decode($result, true);
        }

        $pricing = $data['pricing'];
        $arr = [];
        foreach ($pricing as $key => $value) {
            if($value['service_type'] === 'same_day') {
                $arr[$value['service_type']]['order'] = 2;
            }
            elseif($value['service_type'] === 'overnight') {
                $arr[$value['service_type']]['order'] = 3;
            }
            elseif($value['service_type'] === 'standard') {
                $arr[$value['service_type']]['order'] = 4;
            }
            else{
                $arr[$value['service_type']]['order'] = 1;
            }

            //custom field
            $value['service_name'] = $value['courier_name'];
            $value['etd'] = $value['duration'];
            $value['cost'] = $value['price'];

            $arr[$value['service_type']]['group'] = $value['service_type'];
            $arr[$value['service_type']]['detail'][] = $value;
        }

        uasort($arr, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        return array_values($arr);
    }

    public function order($params){
        $ch = curl_init("https://api.biteship.com/v1/orders");
        $payload = json_encode($params);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:Bearer '.$this->key
        ));
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        $err = curl_error($ch);

        $data = [];
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $data = json_decode($result, true);
        }

        return $data;
    }
}
