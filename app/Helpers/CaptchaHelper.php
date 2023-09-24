<?php


namespace App\Helpers;


use App\Models\UserManagement\RiwayatPenggunaanCaptchaModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class CaptchaHelper
{
    const PANJANG_CAPTCHA = 6;
    const CAPTCHA_GAGAL_DIBUAT = 'Captcha gagal dibuat!';
    const CAPTCHA_EXPIRED_IN_MINUTES = 5;
    const CAPTCHA_BERHASIL_DIBUAT = 'Captcha berhasil dibuat!';
    const IMG_CAPTCHA_GAGAL_DIBUAT = 'Image Captcha gagal dibuat!';
    const FONT_CAPTCHA = 'arial.ttf';
    const CAPTCHA_TIDAK_VALID = 'Captcha tidak valid!';
    const CAPTCHA_TIDAK_SESUAI = 'Captcha tidak sesuai!';
    const CAPTCHA_KADALUARSA = 'Captcha sudah kadaluarsa!';

    public static function generateCaptcha()
    {
        $captcha = self::generateRandomString();
        $tokenCaptcha = [
            'text' => $captcha,
            'expired_at' => Carbon::now()->addMinutes(self::CAPTCHA_EXPIRED_IN_MINUTES)
        ];
        $tokenCaptcha = Crypt::encrypt($tokenCaptcha);

        $imgCaptcha = self::prepareCaptchaImage($captcha);

        if ($imgCaptcha == false) {
            return ResponseHelper::errorResponse(400, self::IMG_CAPTCHA_GAGAL_DIBUAT);
        }

        $result = [
            'token' => $tokenCaptcha,
            'image' => '<img src="data:image/png;base64,' . $imgCaptcha . '"/>',
            'img_url' => "data:image/png;base64,$imgCaptcha"
        ];

        return ResponseHelper::successResponse(self::CAPTCHA_BERHASIL_DIBUAT, $result);
    }

    public static function claimCaptchaToken($captchaToken)
    {
        try {
            $captchaToken = Crypt::decrypt($captchaToken);
            return $captchaToken;
        } catch (\Exception $exception) {
            LogSystemHelper::errorLog($exception);
            return false;
        }
    }

    public static function isCaptchaValid($captchaClaim, $captcha)
    {
        if (!isset($captchaClaim['text'])) {
            return self::CAPTCHA_TIDAK_VALID;
        }

        if (!isset($captchaClaim['expired_at'])) {
            return self::CAPTCHA_TIDAK_VALID;
        }

        $now = Carbon::now();
        $expiredAt = Carbon::parse($captchaClaim['expired_at']);
        if ($now > $expiredAt) {
            return self::CAPTCHA_KADALUARSA;
        }

        if ($captchaClaim['text'] != $captcha) {
            return self::CAPTCHA_TIDAK_SESUAI;
        }

        return true;
    }

    private static function prepareCaptchaImage($captcha)
    {
        try {
            $font = base_path('public') . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR . self::FONT_CAPTCHA;
            $image = imagecreatetruecolor(170, 60);
            $color = imagecolorallocate($image, 238, 51, 58); // red
            $bgcolor = imagecolorallocate($image, 209, 197, 197); // red
            imagefilledrectangle($image, 0, 0, 299, 59, $bgcolor);
            imagettftext($image, 25, 0, 20, 40, $color, $font, $captcha);
            ob_start();
            imagepng($image);
            $imageCaptcha = ob_get_contents();
            ob_end_clean();

            return base64_encode($imageCaptcha);
        } catch (\Exception $exception) {
            LogSystemHelper::errorLog($exception);
            return false;
        }
    }

    private static function generateRandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $captcha = '';
        for ($i = 0; $i < self::PANJANG_CAPTCHA; $i++) {
            $captcha .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $captcha;
    }
}
