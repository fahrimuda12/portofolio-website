<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Validator;

class FileValidationHelper
{
    const TIPE_FILE_TIDAK_DIDUKUNG = 'Tipe file tidak didukung dalam sistem ini!';
    const MESSAGE_VALIDASI_BERHASIL = 'Validasi file berhasil';
    const MIN_SIZE_IN_BYTES = 20;

    public static function validateFile($tipe, $file)
    {
        try {
            $params = [
                'file' => $file
            ];

            switch (strtolower($tipe)) {
                case 'document':
                    $rules = self::getDocumentRules();
                    break;

                case 'image':
                    $rules = self::getImageRules();
                    break;

                case 'spreadsheet':
                    $rules = self::getSpreadsheetRules();
                    break;
                case 'video':
                    $rules = self::getVideoRules();
                    break;
                case 'text':
                    $rules = self::getTextRules();
                    break;
                case 'json':
                    $rules = self::getJsonRules();
                    break;
                default:
                    return ResponseHelper::errorResponse(400, self::TIPE_FILE_TIDAK_DIDUKUNG);
            }

            $validator = Validator::make($params, $rules);
            if ($validator->fails()) {
                $message = "";
                foreach ($validator->getMessageBag()->getMessages() as $num => $item) {
                    foreach ($item as $key => $value) {
                        $message .= "$num:$value <br>";
                    }
                }

                return ResponseHelper::errorResponse(400, $message);
            }

            return ResponseHelper::successResponse(self::MESSAGE_VALIDASI_BERHASIL, true);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    public static function isDoUploadFile($file)
    {
        $isInvalidUpload = !($file instanceof \Illuminate\Http\UploadedFile) ||
            $file->getSize() < self::MIN_SIZE_IN_BYTES ||
            empty($file->getClientOriginalExtension());

        if ($isInvalidUpload) {
            return false;
        }

        return true;
    }

    public static function getVideoRules()
    {
        return ['file' => 'mimes:mp4|max:25000'];
    }

    public static function getDocumentRules()
    {
        return ['file' => 'mimes:pdf|max:25000'];
    }

    public static function getSpreadsheetRules()
    {
        return ['file' => 'mimes:xlx,xlsx,csv|max:25000'];
    }

    public static function getImageRules()
    {
        return ['file' => 'mimes:jpeg,bmp,png,gif,jpg,webp|max:6400'];
    }

    public static function getTextRules()
    {
        return ['file' => 'mimes:txt|max:25000'];
    }

    public static function getJsonRules()
    {
        return ['file' => 'mimes:json|max:25000'];
    }
}
