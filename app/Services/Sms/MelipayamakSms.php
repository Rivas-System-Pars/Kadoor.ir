<?php

namespace App\Services\Sms;

use App\Contracts\SmsContract;
use App\Contracts\SmsNotificationContract;
use Exception;
use Melipayamak\MelipayamakApi;
use Illuminate\Support\Facades\Http;

class MelipayamakSms extends SmsService implements SmsContract, SmsNotificationContract
{
    public function send()
    {
        $method = $this->method();
        $data   = $this->$method();

        $input_data   = $data['input_data'];
        $mobile       = $this->mobile();
        $bodyId       = $data['bodyId'];

        try {

//            $username = "09113356005";
//            $pass = "ANBDP";
//            $response = Http::post('http://api.payamak-panel.com/post/send.asmx/SendByBaseNumber2', [
//                'username' => $username,
//                'password' => $pass,
//                'text' => $input_data,
//                'to' => $mobile,
//                'bodyId' => $bodyId,
//            ]);


            $username  = option('MELIPAYAMAK_PANEL_USERNAME');
            $password  = option('MELIPAYAMAK_PANEL_PASSWORD');
            $api       = new MelipayamakApi($username, $password);
            $sms       = $api->sms('soap');
            $to        = $mobile;
            $text      = implode(';', $input_data);
            $response  = $sms->sendByBaseNumber($text, $to, $bodyId);

//            $username = "09113356005";
//            $pass = "ANBDP";
//            $response = Http::post('http://api.payamak-panel.com/post/send.asmx/SendByBaseNumber2', [
//                'username' => $username,
//                'password' => $pass,
//                'text' => $text,
//                'to' => $to,
//                'bodyId' => $bodyId,
//            ]);

            $message = json_encode($response);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return $message;
    }

    public function verifyCode()
    {
        return [
            'bodyId'       => option('user_verify_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['code']
            ],
        ];
    }

    public function userCreated()
    {
        return [
            'bodyId'       => option('user_register_pattern_code_melipayamak'),
            'input_data'   => [
                '0'   => $this->data['fullname'],
                '1'   => $this->data['username'],
            ],
        ];
    }

    public function orderPaid()
    {
        return [
            'bodyId'       => option('order_paid_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['order_id']
            ],
        ];
    }

    public function userOrderPaid()
    {
        return [
            'bodyId'       => option('user_order_paid_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['order_id']
            ],
        ];
    }

    public function walletAmountDecreased()
    {
        return [
            'bodyId'       => option('wallet_decrease_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['amount']
            ],
        ];
    }

    public function walletAmountIncreased()
    {
        return [
            'bodyId'       => option('wallet_increase_pattern_code_melipayamak'),
            'input_data'   => [
                '0' => $this->data['amount']
            ],
        ];
    }
}
