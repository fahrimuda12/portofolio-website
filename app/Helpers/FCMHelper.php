<?php

namespace App\Helpers;

class FCMHelper
{
    const FCM_SERVER_KEY = 'AAAAr4W4UGE:APA91bEdXpZr8WxyruPdiOPlYp2ssjHvXGGX26aUqwWzJyPK55NndDdhmHBBqS2zRMwa3TJIGalPbU_IWl5posVB4h3s9ICVUrolFdAIFCY631TiJC971ZPdZpv8YyR4R44o8prYk1nx';
    const PRODUCTION_FCM_SERVER_KEY = 'AAAAr4W4UGE:APA91bEdXpZr8WxyruPdiOPlYp2ssjHvXGGX26aUqwWzJyPK55NndDdhmHBBqS2zRMwa3TJIGalPbU_IWl5posVB4h3s9ICVUrolFdAIFCY631TiJC971ZPdZpv8YyR4R44o8prYk1nx';
    const URL_PUSH_NOTIF = 'https://fcm.googleapis.com/fcm/send';

    private static function getServerKey()
    {
        $isProduction = intval(env('IS_PRODUCTION', 0));
        return ($isProduction) ? self::PRODUCTION_FCM_SERVER_KEY : self::FCM_SERVER_KEY;
    }

    public static function pushNotification($params)
    {
        $fields = array();
        $fields['data'] = $params;
        $fields['notification'] = $params;
        $fields['time_to_live'] = 60;

        $target = isset($params['registration_ids']) ? $params['registration_ids'] : null;
        if(is_null($target)) {
            exit;
        }

        if(is_array($target)) {
            $fields['registration_ids'] = $target;
        } else {
            $fields['to'] = $target;
        }
        $fields['content_available'] = true;
        $fields['priority'] = 'high';

        $result = self::curlAdapter(self::URL_PUSH_NOTIF, $fields);
        if($result['code'] == 200) {
            //Jalankan queue utk ngelog push notif yg berhasil dikirim

        }
    }

    private static function curlAdapter($url, $fields)
    {
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.self::getServerKey()
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            return ResponseHelper::errorResponse(500, MessageHelper::errorInvalid('FCM'));
        }

        return ResponseHelper::successResponse(MessageHelper::successStored('Fcm'), null);
    }
}
