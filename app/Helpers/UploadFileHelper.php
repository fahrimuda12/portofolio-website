<?php

namespace App\Helpers;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;


class UploadFileHelper
{
    const PUBLIC_PATH = 'public';
    const GEOJSON_PATH = 'geojson';
    const STORAGE_PATH = 'uploads';
    const STORAGE_IMG_PATH = 'img';
    const NO_THUMBNAIL_FILENAME = 'no-thumbnail.jpg';

    public static function generateThumbnail($videoUrl, $namaThumbnail)
    {
        // Create FFMpeg instance
        $ffmpeg = FFMpeg::create();

        // Open the video file
        $video = $ffmpeg->open($videoUrl);

        // Set the time code where you want to capture the thumbnail
        $timeCode = TimeCode::fromSeconds(2); // Change this to the desired time in seconds
        // Capture the thumbnail
        $frame = $video->frame($timeCode);
        // Specify the path and filename for the thumbnail with ".jpg" extension
        $thumbnailPath = base_path(self::PUBLIC_PATH . TDS . self::STORAGE_PATH . TDS) . $namaThumbnail . '.jpg';

        try {
            // Save the thumbnail to a file
            $frame->save($thumbnailPath);
            return  $namaThumbnail . '.jpg';
            // echo 'Thumbnail saved successfully: ' . $thumbnailPath . PHP_EOL;
        } catch (\Exception $exception) {
            LogSystemHelper::errorLog($exception);
            return false;
        }
    }

    public static function uploadFile($filename, $file)
    {
        try {
            self::initStorageDirectory();

            $file->move(self::STORAGE_PATH, $filename);
            return true;
        } catch (\Exception $exception) {
            LogSystemHelper::errorLog($exception);
            return false;
        }
    }

    public static function uploadFileGeoJSON($filename, $file, $path)
    {
        try {
            self::initGeoJsonDirectory();

            $file->move(self::GEOJSON_PATH.$path, $filename);
            return true;
        } catch (\Exception $exception) {
            LogSystemHelper::errorLog($exception);
            return false;
        }
    }

    public static function removeFile($filename)
    {
        try {
            unlink(self::getLocalUrl($filename));
            return true;
        } catch (\Exception $exception) {
            LogSystemHelper::errorLog($exception);
            return false;
        }
    }

    // use endpoin method
    public static function getUrlApiEndpoint($filename)
    {
        $isUseIndexPhp = intval(env('IS_USE_INDEX_PHP', 0));
        $arr = explode('.', $filename);
        if (count($arr) == 2) {
            $url = self::urlValidated('get-file' . TDS . $arr[0] . TDS . $arr[1]);
            return $isUseIndexPhp == 1 ? $url : str_replace("/index.php", "", $url);
        }

        return self::getNoThumbnailUrlApiEndpoint();
    }

    public static function getGeojsonUrlApiEndpoint($state, $dukcapil)
    {
        $isUseIndexPhp = intval(env('IS_USE_INDEX_PHP', 0));
        $url = self::urlValidated('get-geojson' . TDS . $state . TDS . str_replace('.', '', $dukcapil));
        return $isUseIndexPhp == 1 ? $url : str_replace("/index.php", "", $url);
    }

    public static function getNoThumbnailUrlApiEndpoint()
    {
        $isUseIndexPhp = intval(env('IS_USE_INDEX_PHP', 0));
        $url = self::urlValidated('get-thumbnail/no-thumbnail/jpg');
        return $isUseIndexPhp == 1 ? $url : str_replace("/index.php", "", $url);
    }

    public static function getImgUrlApiEndpoint($filename)
    {
        $isUseIndexPhp = intval(env('IS_USE_INDEX_PHP', 0));
        $arr = explode('.', $filename);
        if (count($arr) == 2) {
            $url = self::urlValidated('get-image' . TDS . $arr[0] . TDS . $arr[1]);
            return $isUseIndexPhp == 1 ? $url : str_replace("/index.php", "", $url);
        }

        return null;
    }

    // use old version
    public static function getUrl($filename)
    {
        $url = self::urlValidated(self::getLocalUrl($filename));
        return str_replace("/index.php", "", $url);
    }

    public static function getNoThumbnailUrl()
    {
        $url = self::urlValidated('assets'. TDS . self::getImgLocalUrl(self::NO_THUMBNAIL_FILENAME));
        return str_replace("/index.php", "", $url);
    }

    public static function getImgUrl($filename)
    {
        $url = self::urlValidated(self::getImgLocalUrl($filename));
        return str_replace("/index.php", "", $url);
    }

    // real url
    public static function getRealUrl($filename)
    {
        return self::publicPath() . TDS . self::getLocalUrl($filename);
    }

    public static function getGeoJsonRealUrl($folder, $dukcapil)
    {
        $baseGeoJsonPath = self::publicPath() . TDS . self::GEOJSON_PATH . TDS . $folder;
        $allFilesInFolder = scandir($baseGeoJsonPath);
        $allFilesInFolderFormatted = array_map(function ($item) {
            return $item . '-' . strtolower(str_replace('.', '', $item));
        }, $allFilesInFolder);

        $filtered = array_values(array_filter($allFilesInFolderFormatted, fn ($item) => strpos($item, str_replace('.', '', $dukcapil)) != false));

        if (count($filtered) == 0) {
            return $baseGeoJsonPath . TDS . $dukcapil . '.geo.json';
        }

        $realFileName = clean_explode(($filtered[0] ?? null), '-')[0] ?? null;

        return $baseGeoJsonPath . TDS . $realFileName;
    }

    public static function getNoThumbnailRealUrl()
    {
        return self::publicPath() . TDS . self::getImgLocalUrl(self::NO_THUMBNAIL_FILENAME);
    }

    public static function getRealImgUrl($filename)
    {
        return self::publicPath() . TDS . self::getImgLocalUrl($filename);
    }

    public static function getLocalUrl($filename)
    {
        return self::STORAGE_PATH . TDS . $filename;
    }

    public static function getGeojsonLocalUrl($state, $dukcapil)
    {
        return self::GEOJSON_PATH . TDS . $state . TDS . $dukcapil . '.geo.json';
    }

    public static function getImgLocalUrl($filename)
    {
        return self::STORAGE_IMG_PATH . TDS . $filename;
    }

    private static function initStorageDirectory()
    {
        if (!file_exists(self::STORAGE_PATH)) {
            mkdir(self::STORAGE_PATH, 0777, true);
        }
    }

    private static function initGeoJsonDirectory()
    {
        if (!file_exists(self::GEOJSON_PATH)) {
            mkdir(self::GEOJSON_PATH, 0777, true);
        }
    }

    private static function publicPath()
    {
        return base_path() . TDS . self::PUBLIC_PATH;
    }

    private static function urlValidated($urlPath)
    {
        $isUnderProxy = intval(env('IS_UNDER_PROXY', 0));
        if ($isUnderProxy) {
            $url = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https:' : 'http:') . TDS . TDS . $_SERVER['HTTP_X_FORWARDED_HOST'] . $_SERVER['SCRIPT_NAME'] . TDS . $urlPath :
                config('app.url') . TDS . $urlPath;
        } else {
            $url = url($urlPath);
        }

        return $url;
    }
}
