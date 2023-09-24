<?php

namespace App\Models\Main;

use App\Helpers\UploadFileHelper;
use App\Models\StringPrimaryKeyHiddenTimestampModel;

class ProjectModel extends StringPrimaryKeyHiddenTimestampModel
{
    protected $table = 'project';
    protected $primaryKey = 'project_id';

    // public function scopeFilter($query, array $filters)
    // {
    //     $query->when($filters['cari'] ?? false, function ($query, $cari) {
    //         return $query->where(function ($query) use ($cari) {
    //             $query->where('judul', 'ilike', '%' . $cari . '%');
    //             $query->where('author', 'ilike', '%' . $cari . '%');
    //             $query->where('deskripsi', 'ilike', '%' . $cari . '%');
    //         });
    //     });

    //     $query->when($filters['klasifikasi_map_static_id'] ?? false, function ($query, $klasifikasiMapId) {
    //         return $query->where(function ($query) use ($klasifikasiMapId) {
    //             $query->where('klasifikasi_map_static_id', $klasifikasiMapId);
    //         });
    //     });

    //     $query->when($filters['tahun'] ?? false, function ($query, $tahun) {
    //         return $query->where(function ($query) use ($tahun) {
    //             $query->where('tahun', $tahun);
    //         });
    //     });
    // }

    public function getFotoUrlAttribute()
    {
        if (is_null($this->foto)) {
            return UploadFileHelper::getNoThumbnailUrlApiEndpoint();
        }
        $url = UploadFileHelper::getUrlApiEndpoint($this->foto);
        $urlLocal = UploadFileHelper::getLocalUrl($this->foto);
        if (file_exists($urlLocal)) {
            return $url;
        }
        return UploadFileHelper::getNoThumbnailUrlApiEndpoint();
    }

    public function getFotoLinkAttribute()
    {
        if (is_null($this->foto)) {
            return UploadFileHelper::getNoThumbnailUrl();
        }

        $url = UploadFileHelper::getUrl($this->foto);
        $urlLocal = UploadFileHelper::getLocalUrl($this->foto);
        if (file_exists($urlLocal)) {
            return $url;
        }

        return UploadFileHelper::getNoThumbnailUrl();
    }

    public function getThumbnailUrlAttribute()
    {
        if (is_null($this->thumbnail)) {
            return UploadFileHelper::getNoThumbnailUrlApiEndpoint();
        }
        $url = UploadFileHelper::getUrlApiEndpoint($this->thumbnail);
        $urlLocal = UploadFileHelper::getLocalUrl($this->thumbnail);
        if (file_exists($urlLocal)) {
            return $url;
        }
        return UploadFileHelper::getNoThumbnailUrlApiEndpoint();
    }

    public function getThumbnailLinkAttribute()
    {
        if (is_null($this->thumbnail)) {
            return UploadFileHelper::getNoThumbnailUrl();
        }

        $url = UploadFileHelper::getUrl($this->thumbnail);
        $urlLocal = UploadFileHelper::getLocalUrl($this->thumbnail);
        if (file_exists($urlLocal)) {
            return $url;
        }

        return UploadFileHelper::getNoThumbnailUrl();
    }
}
