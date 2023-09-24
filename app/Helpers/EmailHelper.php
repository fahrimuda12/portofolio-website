<?php

namespace App\Helpers;

use App\Jobs\KirimEmailNotificationJob;
use App\Helpers\MessageHelper;
use App\Helpers\DateHelper;
use App\Mail\NotifEmail;
use App\Models\UserManagement\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    public static function prepareEmailNotification($emailTujuan, $nama, $topik, $judul, $isi, $alamatTkp)
    {
        if (strlen($emailTujuan) == 0) {
            LogSystemHelper::stringLogError(MessageHelper::errorRequired('email'), 'notification');
            return false;
        }

        if (strlen($nama) == 0) {
            LogSystemHelper::stringLogError(MessageHelper::errorRequired('nama'), 'notification');
            return false;
        }

        if (strlen($topik) == 0) {
            LogSystemHelper::stringLogError(MessageHelper::errorRequired('topik'), 'notification');
            return false;
        }

        if (strlen($judul) == 0) {
            LogSystemHelper::stringLogError(MessageHelper::errorRequired('judul'), 'notification');
            return false;
        }

        if (strlen($isi) == 0) {
            LogSystemHelper::stringLogError(MessageHelper::errorRequired('isi'), 'notification');
            return false;
        }

        if (strlen($alamatTkp) == 0) {
            LogSystemHelper::stringLogError(MessageHelper::errorRequired('alamat tkp'), 'notification');
            return false;
        }

        dispatch(new KirimEmailNotificationJob($emailTujuan, [
            'nama' => $nama,
            'topik' => $topik,
            'judul' => $judul,
            'isi' => $isi,
            'alamat_tkp' => $alamatTkp,
        ]));
        return true;
    }

    public static function sendEmail($userId, $judul, $message, $sumberNotifikasi, $jenisNotificationId, $subject = "notif-email", $extraParams = [])
    {
        $user = UserModel::find($userId);
        $email = $user->email ?? null;
        $nama = $user->nama ?? null;
        Mail::to($email)->send(new NotifEmail($judul, $message, $nama, $sumberNotifikasi, $subject, $extraParams));
        Log::info("berhasil mengirim email");
    }
}
