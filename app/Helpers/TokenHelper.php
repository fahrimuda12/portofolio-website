<?php


namespace App\Helpers;

use App\Models\UserManagement\RiwayatLupaPassword;
use App\Models\UserManagement\UserModel;
use Carbon\Carbon;

class TokenHelper
{
    const PANJANG_ARRAY_TOKEN = 5;
    const MESSAGE_TOKEN_EXPIRED = "Token user sudah expired";

    public static function prepareToken($userModel)
    {
        $token = DandelionTokenHelper::prepareToken($userModel);
        return $token;
    }

    public static function prepareTokenReset($userModel, $kode)
    {

        $token = DandelionTokenHelper::prepareTokenResetPassword($userModel, $kode);
        return $token;
    }

    public static function isTokenValid($token)
    {
        if(strlen($token) == 0) {
            return false;
        }

        $claimToken = DandelionTokenHelper::claimToken($token);
        $arrayString = explode("|", $claimToken);
        if(count($arrayString) < self::PANJANG_ARRAY_TOKEN) {
            return false;
        }

        $expiredTime = isset($arrayString[4]) ? $arrayString[4] : null;
        if(is_null($expiredTime)) {
            return null;
        }

        $now = Carbon::now()->timestamp;
        if($now > $expiredTime) {
            LogSystemHelper::stringLogError(self::MESSAGE_TOKEN_EXPIRED, 'validasi-token');
            return false;
        }

        $userId = isset($arrayString[2]) ? $arrayString[2] : null;
        $userId = is_null($userId) ? null : substr($userId, 9);
        return (!is_null($userId)) && strlen($userId) > 0;
    }

    public static function getUserFromToken($token)
    {
        if(self::isTokenValid($token)) {
            $claimToken = DandelionTokenHelper::claimToken($token);
            $arrayString = explode("|", $claimToken);
            if(count($arrayString) < self::PANJANG_ARRAY_TOKEN) {
                return false;
            }

            $userId = isset($arrayString[2]) ? $arrayString[2] : null;
            $userId = is_null($userId) ? null : substr($userId, 9);
            return UserModel::query()->find($userId);
        } else {
            return null;
        }
    }

    public static function getVerifikasiReset($token)
    {

        if (self::isTokenValid($token)) {
            $claimToken = DandelionTokenHelper::claimToken($token);
            $arrayString = explode("|", $claimToken);
            if(count($arrayString) < self::PANJANG_ARRAY_TOKEN) {
                return null;
            }

            $userId = isset($arrayString[2]) ? $arrayString[2] : null;
            $userId = is_null($userId) ? null : substr($userId, 9);
            $kode = isset($arrayString[3]) ? $arrayString[3] : null;
            if(!RiwayatLupaPassword::where('kode_reset',$kode)->where('is_kadaluarsa', 0)->exists()){
                return null;

            }
            $result = UserModel::query()->where('user_id', $userId)->first();
            return ["kode_verifikasi" => $kode, "data" => $result];
        } else {
            return null;
        }
    }
}
