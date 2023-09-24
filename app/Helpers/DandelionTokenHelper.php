<?php

namespace App\Helpers;

use Carbon\Carbon;

class DandelionTokenHelper
{
    const PREFIX_SALT_LENGTH = 60;
    const SUFFIX_SALT_LENGTH = 75;
    const BASE64_SALT_LENGTH = 20;

    public static function prepareToken($userModel)
    {
        /** Prepare credential data */
        $preparedString = "BLZ" . rand(1, 1000000) . "|STK" . rand(1, 100000) . "|ALZ" . rand(100000, 999999) . $userModel->user_id . "|" . Carbon::now()->timestamp . "|" . Carbon::now()->endOfDay()->timestamp;
        $preparedString = base64_encode($preparedString);
        $base64Salt = StringHelper::generateRandomString(self::BASE64_SALT_LENGTH);
        $preparedString = $base64Salt . $preparedString;
        $preparedString = base64_encode($preparedString);
        $base64SaltLevel2 = StringHelper::generateRandomString(self::BASE64_SALT_LENGTH);
        $preparedString = $base64SaltLevel2 . $preparedString;
        $preparedString = base64_encode($preparedString);

        /** Adding prefix and suffix */
        $prefix = StringHelper::generateRandomString(self::PREFIX_SALT_LENGTH);
        $suffix = StringHelper::generateRandomString(self::SUFFIX_SALT_LENGTH);

        /** Generate token */
        $token = $prefix . $preparedString . $suffix;
        return $token;
    }

    public static function prepareTokenResetPassword($userModel, $kode)
    {
        /** Prepare credential data */
        $expired = Carbon::now()->addMinutes(45)->timestamp;
        $preparedString = "BLZ" . rand(1, 1000000) . "|STK" . rand(1, 100000) . "|ALZ" . rand(100000, 999999) . $userModel->user_id . "|" . $kode . "|" . $expired;
        $preparedString = base64_encode($preparedString);
        $base64Salt = StringHelper::generateRandomString(self::BASE64_SALT_LENGTH);
        $preparedString = $base64Salt . $preparedString;
        $preparedString = base64_encode($preparedString);
        $base64SaltLevel2 = StringHelper::generateRandomString(self::BASE64_SALT_LENGTH);
        $preparedString = $base64SaltLevel2 . $preparedString;
        $preparedString = base64_encode($preparedString);

        /** Adding prefix and suffix */
        $prefix = StringHelper::generateRandomString(self::PREFIX_SALT_LENGTH);
        $suffix = StringHelper::generateRandomString(self::SUFFIX_SALT_LENGTH);

        /** Generate token */
        $token = $prefix . $preparedString . $suffix;
        return ["token" => $token, "expired_at" => $expired];
    }

    public static function claimToken($token)
    {
        /** Remove prefix and suffix */
        $token = substr($token, self::PREFIX_SALT_LENGTH);
        $token = substr($token, 0, -self::SUFFIX_SALT_LENGTH - 1);

        /** Decode level 2 base 64 */
        $token = base64_decode($token);

        /** Remove level 2 salt prefix */
        $token = substr($token, self::BASE64_SALT_LENGTH);

        /** Decode level 1 base64 */
        $token = base64_decode($token);

        /** Get original data */
        $token = substr($token, self::BASE64_SALT_LENGTH);

        $token = base64_decode($token);
        return $token;
    }
}
