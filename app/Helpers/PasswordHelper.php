<?php


namespace App\Helpers;

use App\Models\UserManagement\RiwayatLupaPassword;
use App\Models\UserManagement\UserModel;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class PasswordHelper
{
    const PANJANG_SALT = 32;

    public static function generateSalt($plainPassword)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $saltPassword = "";
        $arrayPlainPassword = str_split($plainPassword);

        while (strlen($saltPassword) < self::PANJANG_SALT) {
            $randomChar = $chars[mt_rand(0, strlen($chars) - 1)];
            if(in_array($randomChar, $arrayPlainPassword)) {
                continue;
            }

            $saltPassword .= $randomChar;
        }

        return $saltPassword;
    }

    public static function preparePassword($salt, $plainPassword)
    {
        return (base64_encode($salt.$plainPassword));
    }

    public static function generateLinkResetPassword($email,$kode)
    {
        try {
            $user = UserModel::where("email", $email)->where("user_group_id", '!=', 01)->first();
            if (!$user) {
                return ResponseHelper::errorResponse(400, "User tidak ditemukan");
            }
            $token = TokenHelper::prepareTokenReset($user, $kode);

            $link = env("APP_URL") . "/reset-password/verifikasi?token=" . $token["token"];
            return ResponseHelper::successResponse("Berhasil di generate", ["link" => $link, "data" => $user, "token" => $token]);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }
}
