<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Publik\PublikController;
use App\Repositories\Main\PendaftaranMobileRepository;
use App\Repositories\UserManagement\ResetPasswordRepository;
use Illuminate\Http\Request;

class ResetPasswordController extends PublikController
{
    public function simpan(Request $request)
    {
        $params = [
            'password_baru' => $request->input('password_baru'),
            'konfirmasi_password_baru' => $request->input('konfirmasi_password_baru'),
            'token' => $request->input('token'),
            // 'kode_verifikasi' => $request->input('kode_verifikasi'),
        ];
        $params = array_merge($params, $this->getDefaultParameter($request));
        $result = (new ResetPasswordRepository())->aksiResetPassword($params);
        return response()->json($result, $result['code']);
    }

    public function request(Request $request)
    {
        $params = [
            'email' => $request->input('email'),
        ];
        $params = array_merge($params, $this->getDefaultParameter($request));
        $result = (new ResetPasswordRepository())->aksiRequest($params);
        return response()->json($result, $result['code']);
    }

    public function verifikasi(Request $request)
    {
        $params = [
            'token' => $request->input('token'),
        ];

        $params = array_merge($params, $this->getDefaultParameter($request));
        $result = (new ResetPasswordRepository())->aksiVerifikasi($params);
        if ($result['code'] != 200) {
            return redirect(env('APP_URL_FRONTOFFICE') . '/komcad');
        }
        return redirect(env('APP_URL_FRONTOFFICE') . '/komcad/reset-password?token=' . $params['token'] . '&kode_verifikasi=' . $result['data']['kode_verifikasi']);
    }
}
