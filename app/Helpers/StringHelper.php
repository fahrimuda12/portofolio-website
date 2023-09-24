<?php


namespace App\Helpers;


class StringHelper
{
    const ANGKA_STRING = ['0','1','2','3','4','5','6','7','8','9'];
    
    public static function generateRandomString($length = 32, $hurufSaja = false)
    {
        $characters = ($hurufSaja ? '' : '0123456789').'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stringResult = '';
        for ($i = 0; $i < $length; $i++) {
            $stringResult .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $stringResult;
    }

    public static function getMime($format)
    {
        switch ($format) {
            case 'pdf':
                return 'application/pdf';
                break;
            
            default:
                return null;
                break;
        }
    }

    public static function isBase64Encoded($str)
    {
        if (intval($str) == $str) {
            return false;
        }

        if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $str) === false) {
            return false;
        }

        $decoded = base64_decode($str, true);
        if ($decoded === false) {
            return false;
        }

        $encoding = mb_detect_encoding($decoded);
        if (!in_array($encoding, ['UTF-8', 'ASCII'], true)) {
            return false;
        }

        return $decoded !== false && base64_encode($decoded) === $str;
    }

    public static function stringHighlighter($string, $keywordHighlighted = [])
    {
        if (count($keywordHighlighted) == 0) {
            return $string;
        }

        $keywordHighlighted = array_map(function ($str) {
            return strtolower($str);
        }, $keywordHighlighted);

        $highlightedText = array_map(function ($str) use ($keywordHighlighted){
            $cleanText = strtolower(preg_replace("/[^A-Za-z]/",'',$str));
            foreach ($keywordHighlighted as $keyword) {
                if ($cleanText == $keyword) {
                    return '<mark>'.$str.'</mark>';
                }
                elseif (strpos($cleanText, $keyword) > -1) {
                    return str_replace($keyword, '<mark>'.$keyword.'</mark>', $str);
                }
            }
            return $str;
        }, clean_explode($string, ' '));

        return implode(' ', $highlightedText);
    }

    public static function utf8Encoder($string)
    {
        try {
            return mb_convert_encoding($string, 'UTF-8');
        } catch (\Exception $e) {
            return $string;
        }
    }

    public static function capitalizeEachWords($string)
    {
        return \Illuminate\Support\Str::title($string);
    }

    public static function isKodeHexaMirip($kodeWarna1, $kodeWarna2)
    {
        if (substr($kodeWarna1, 0, 1) == '#') {
            $kodeWarna1 = substr($kodeWarna1, 1);
        }
        
        if (substr($kodeWarna2, 0, 1) == '#') {
            $kodeWarna2 = substr($kodeWarna2, 1);
        }

        $pecahKode1 = str_split($kodeWarna1, 2);
        $pecahKode2 = str_split($kodeWarna2, 2);

        if (count($pecahKode1) != count($pecahKode2)) {
            return false;
        }

        $segmenMirip = 0;
        for ($i=0; $i < count($pecahKode1); $i++) {
            $beda = abs(hexdec($pecahKode1[$i]) - hexdec($pecahKode2[$i]));

            if ($beda < 28) { //threshold kesamaan warna, semakin kecil nilainya semakin tidak ketat deteksinya
                $segmenMirip++;
            }
        }

        return ($segmenMirip >= 2);
    }

    public static function satuanAngka($number, $isSortLabel = 0)
    {
        $returnLabel = $number;
        $pembagi = null;
        $strLabel = null;
        $strShortLabel = null;

        if ($number >= 1000000) {
            $pembagi = 1000000;
            $strLabel = 'Juta';
            $strShortLabel = 'Jt';
        } elseif ($number >= 1000) {
            $pembagi = 1000;
            $strLabel = 'Ribu';
            $strShortLabel = 'Rb';
        }

        if (!is_null($pembagi)) {
            $returnLabel = number_format(($number / $pembagi), 2, ',') . ' ' . ($isSortLabel ? $strShortLabel : $strLabel);
        }

        return $returnLabel;
    }

    public static function toFloat($stringDesimal)
    {
        return floatval(str_replace(',', '.', $stringDesimal));
    }

    public static function toInt($stringInt)
    {
        return intval(str_replace('.', '', $stringInt));
    }
}
