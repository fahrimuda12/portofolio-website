<?php


namespace App\Helpers;

use DateTime;
use Carbon\Carbon;

class DateHelper
{
    public static function prepareIndonesianFormat($date, $bulan3Karakter = false, $minified = false)
    {
        $arrayDate = explode("-", $date);
        if(count($arrayDate) != 3) {
            return '-';
        }

        $bulan = self::getBulan($arrayDate[1]);
        return last($arrayDate).' '.($bulan3Karakter ? substr($bulan, 0,3) : $bulan). (!$minified ? ' '.collect($arrayDate)->first() : '');
    }

    public static function prepareTimeIndonesianFormat($time)
    {
        $result = DateTime::createFromFormat('H:i:s', $time);
        if (!is_bool($result)) {
            return $result->format('H:i');
        }

        return null;
    }

    public static function prepareDayTimeFormatLengkap($dateTime, $withDate = true, $withDay = true, $withTime = true, $bulan3Karakter = false)
    {
        $arrayDateTime = explode(' ', $dateTime);
        $date = isset($arrayDateTime[0]) ? $arrayDateTime[0] : date('Y-m-d');
        $time = isset($arrayDateTime[1]) ? $arrayDateTime[1] : date('H:i:s');

        $day = DateTime::createFromFormat('Y-m-d', $date)->format('D');
        $day = self::getArrayShortDay()[strtolower($day)];

        if ($withDate) {
            $date = self::prepareIndonesianFormat($date, $bulan3Karakter);
        }

        if ($withTime) {
            $time = self::prepareTimeIndonesianFormat($time);
        }

        return trim(($withDay ? $day.', ' : '').($withDate ? $date.' ' : '').($withTime ? $time : ''));
    }

    public static function getSelisihWaktu($waktuAwal, $waktuAkhir, $isDalamMenit = false)
    {
        $waktuAwal = Carbon::parse($waktuAwal);
        $waktuAkhir = Carbon::parse($waktuAkhir);

        $diffInDay = $waktuAkhir->diffInDays($waktuAwal);
        $diffInHour = $waktuAkhir->diffInHours($waktuAwal);
        $diffInMinute = $waktuAkhir->diffInMinutes($waktuAwal);

        return ($isDalamMenit) ? 
            ($diffInDay*24*60) + ($diffInHour*60) + $diffInMinute : 
            [
                'diffInDay' => $diffInDay,
                'diffInHour' => $diffInHour,
                'diffInMinute' => $diffInMinute
            ];
    }

    public function getHariFormatIndonesia($date)
    {
        $day = DateTime::createFromFormat('Y-m-d', $date)->format('D');
        $opsiHari = self::getArrayShortDay();
        return isset($opsiHari[strtolower($day)]) ? $opsiHari[strtolower($day)] : '-';
    }

    public static function getBulan($index)
    {
        $opsiBulan = self::getArrayBulan();
        return isset($opsiBulan[$index]) ? $opsiBulan[$index] : '-';
    }

    public static function getArrayBulan()
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }

    public static function getArrayShortDay()
    {
        return [
            'mon' => 'Senin',
            'tue' => 'Selasa',
            'wed' => 'Rabu',
            'thu' => 'Kamis',
            'fri' => 'Jumat',
            'sat' => 'Sabtu',
            'sun' => 'Minggu'
        ];
    }

    public static function getSelisihWaktuFormatted($dateTime)
    {
        $now = Carbon::now();

        $diffInDay = $now->diffInDays($dateTime);
        if($diffInDay > 0) {
            return "$diffInDay hari yang lalu";
        }

        $diffInHour = $now->diffInHours($dateTime);
        if($diffInHour > 0) {
            return "$diffInHour jam yang lalu";
        }

        $diffInMinute = $now->diffInMinutes($dateTime);
        if($diffInMinute > 0) {
            return "$diffInMinute menit yang lalu";
        }

        $diffInSecond = $now->diffInSeconds($dateTime);
        return "$diffInSecond detik yang lalu";
    }

    public static function getUsia($tanggalLahir)
    {
        $now = Carbon::now();
        $tanggalLahir = Carbon::parse($tanggalLahir)->toDateString();
        $diffInYear = $now->diffInYears($tanggalLahir);
        return $diffInYear;
    }

}
