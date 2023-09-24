<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\UploadFileHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function getDefaultParameter(Request $request)
    {
        return [
            'ip' => $request->ip(),
            'ip_server' => $request->server('SERVER_ADDR'),
            'user_agent' => $request->input('user_agent')
        ];
    }

    public function getGeoJson($folder, $dukcapil)
    {
        return !file_exists(UploadFileHelper::getGeoJsonRealUrl($folder, $dukcapil)) ?
            null :
            response()->download(UploadFileHelper::getGeoJsonRealUrl($folder, $dukcapil));
    }

    public function getFile($fileName, $extension)
    {
        return !file_exists(UploadFileHelper::getRealUrl($fileName.'.'.$extension)) ?
            $this->getThumbnail() :
            response()->download(UploadFileHelper::getRealUrl($fileName.'.'.$extension));
    }

    public function getImg($fileName, $extension)
    {
        return !file_exists(UploadFileHelper::getRealImgUrl($fileName . '.' . $extension)) ?
            $this->getThumbnail() :
            response()->download(UploadFileHelper::getRealImgUrl($fileName . '.' . $extension));
    }

    public function getThumbnail()
    {
        return response()->download(UploadFileHelper::getNoThumbnailRealUrl());
    }

    public function getNoThumbnail()
    {
        return response()->download(UploadFileHelper::getNoThumbnailRealUrl());
    }

    public function playground()
    {

    }
}
