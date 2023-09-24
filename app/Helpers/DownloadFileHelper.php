<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class DownloadFileHelper
{

    const NAMA_FILE_TIDAK_ADA = 'Nama file tidak dikenali!';
    const CONTENT_TYPE_TIDAK_VALID = 'Tipe file tidak valid!';

    public static function generateResponseFile($filename)
    {
        if(strlen($filename) == 0) {
            return ResponseHelper::errorResponse(400, self::NAMA_FILE_TIDAK_ADA);
        }

        $path = 'uploads'.DIRECTORY_SEPARATOR.$filename;
        if (!file_exists($path)) {
            return ResponseHelper::errorResponse(400, self::NAMA_FILE_TIDAK_ADA);
        }
        
        $file = File::get($path);
        $response = Response::make($file, 200);

        $arrayFilename = explode(".", $filename);
        $extension = last($arrayFilename);
        $headerContentType = self::generateHeaderValueByExtension($extension);
        if(is_null($headerContentType)) {
            LogSystemHelper::stringLogError(self::CONTENT_TYPE_TIDAK_VALID, 'download-konten');
            return ResponseHelper::errorResponse(400, self::CONTENT_TYPE_TIDAK_VALID);
        }

        $response->header('Content-Type', $headerContentType);
        return $response;
    }

    private static function generateHeaderValueByExtension($extension)
    {
        switch (strtolower($extension)) {
            case 'xls':
                return 'application/vnd.ms-excel';
                break;
            case 'xlsx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'ppt':
                return 'application/vnd.ms-powerpoint';
                break;
            case 'pptx':
                return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                break;
            case 'docx':
            case 'doc' :
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'pdf' :
                return 'application/pdf';
                break;
            case 'gif':
                return 'image/gif';
                break;
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
                break;
            case 'png':
                return 'image/png';
                break;
            case 'tiff':
                return 'image/tiff';
                break;
            case 'mpeg':
                return 'video/mpeg';
                break;
            case 'mp4':
                return 'video/mp4';
                break;
            case 'wmv':
                return 'video/x-ms-wmv';
                break;
            case 'flv':
                return 'video/x-flv';
                break;
            case 'webm':
                return 'video/webm';
                break;
            case 'webp':
                return 'image/webp';
                break;
            default:
                return null;
                break;
        }
    }
}
