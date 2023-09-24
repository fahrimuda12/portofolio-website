<?php
include 'constanta.php';

if (!function_exists('public_path')) {

    /**
     * Return the path to public dir
     * @param null $path
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}
if (!function_exists('app_path')) {

    /**
     * Return the path to public dir
     * @param null $path
     * @return string
     */
    function app_path($path = null)
    {
        return rtrim(app()->basePath('app/' . $path), '/');
    }
}
function controller_path($controller, $modul = '')
{
    if(strlen($modul) == 0) {
        $path = path_generator($controller);
    } else {
        $modulDir = get_modul_directory($modul);
        $path = path_generator($modulDir.TDS.$controller);
    }

    return $path;
}

function get_modul_directory($modul='')
{
    if (empty($modul)) {
        return '';
    }

    $modul = strtolower($modul);
    $dirName = implode('', array_map('ucfirst', clean_explode($modul, '-')));
    return $dirName;
}


function path_generator($path)
{
    switch (get_active_os()) {
        case 'darwin':
        case 'mac':
        case 'unix':
        case 'linux':
            $path = str_replace("/", "\\", $path);
            break;
        default:
            break;
    }

    return $path;
}


function censor($string, $length = 4)
{
    $newChar = '';
    for ($i=0; $i<$length; $i++) {
        $newChar .= 'x';
    }

    return substr_replace($string, $newChar, -$length);
}

function get_sumber_data($isMobileRequest = null)
{
    return is_null($isMobileRequest) ? (is_mobile() ? 'mobile' : 'web') : (intval($isMobileRequest) ? 'mobile' : 'web');
}

function get_active_os()
{
    return strtolower(php_uname('s'));
}

function is_mobile()
{
    return preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|darwin|wos)/i", strtolower($_SERVER["HTTP_USER_AGENT"]));
}

function active_user($params)
{
    $token = isset($params['token']) ? $params['token'] : '';
    $activeUser = \App\Helpers\TokenHelper::getUserFromToken($token);

    return $activeUser;
}

//! support separator array & string
function clean_explode($stringData, $separator=';')
{
    if (is_array($separator)) {
        $arraySep = $separator;
        foreach ($arraySep as $sep) {
            if (strpos($stringData, $sep) > -1) {
                $separator = $sep;
                break;
            }
        }

        if (is_array($separator)) {
            $separator = $separator[0];
        }
    }

    $tempArray = explode($separator, $stringData);
    $trimmedArray = array_map('trim', $tempArray);
    $cleanedArray = array_filter($trimmedArray, fn($item) => !is_null($item) && $item !== '');
    return array_values($cleanedArray);
}

function validate_array_input($arrayData, $requiredKolom = [])
{
    if (count($arrayData) == 0) {
        return false;
    }

    $isValid = true;
    foreach ($arrayData as $elemen) {
        $keys = array_keys($elemen);
        foreach ($requiredKolom as $data) {
            if (!in_array($data, $keys)) {
                $isValid = false;
                break;
            }
        }

        if (!$isValid) {
            break;
        }
    }

    return $isValid;
}

function set_connection($koneksi, $userId = null)
{
    return \App\Helpers\DdlHelper::setConnection($koneksi, $userId);
}
