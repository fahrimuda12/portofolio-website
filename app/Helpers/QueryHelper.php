<?php

namespace App\Helpers;

class QueryHelper
{
    /**
     * $query => query builder objek
     * $kolomFilter => array kolom pada db, misal ['jumlah_min', 'jumlah_max']
     * $rangeFilter => array range angka, misal [100, 200]
     * return None
     */
    public static function filterRangeAngkaWith2Column(&$query, $kolomFilter = [], $rangeFilter = [])
    {
        if (count($kolomFilter) != 2 || count($rangeFilter) != 2) {
            return;
        }

        $kolomMin = $kolomFilter[0];
        $kolomMax = $kolomFilter[1];

        $rangeMin = $rangeFilter[0];
        $rangeMax = $rangeFilter[1];

        if (strlen($rangeMin) > 0 && strlen($rangeMax) > 0) {
            $rangeMin = intval($rangeMin);
            $rangeMax = intval($rangeMax);

            $query->where(function ($query) use ($rangeMin, $rangeMax, $kolomMin, $kolomMax) {
                $query->orWhere($kolomMin, '>=', $rangeMin);
                $query->orWhere($kolomMax, '<=', $rangeMax);
            });

            $query->where(function ($query) use ($rangeMin, $rangeMax, $kolomMin, $kolomMax) {
                $query->where(function ($query) use ($rangeMin, $rangeMax, $kolomMin) {
                    $query->where($kolomMin, '>=', $rangeMin);
                    $query->where($kolomMin, '<=', $rangeMax);
                });
                $query->orWhere(function ($query) use ($rangeMin, $rangeMax, $kolomMax) {
                    $query->where($kolomMax, '>=', $rangeMin);
                    $query->where($kolomMax, '<=', $rangeMax);
                });
            });
        } elseif ((strlen($rangeMin) > 0 && strlen($rangeMax) == 0) || (strlen($rangeMin) == 0 && strlen($rangeMax) > 0)) {
            if (strlen($rangeMin) > 0) {
                $query->where($kolomMin, '>=', intval($rangeMin));
            }

            if (strlen($rangeMax) > 0) {
                $query->where($kolomMax, '<=', intval($rangeMax));
            }
        }

        return;
    }

    /**
     * $query => query builder objek
     * $kolomFilter => array kolom pada db, misal ['tanggal_min', 'tanggal_max']
     * $rangeFilter => array range tanggal Y-m-d, misal ['2020-01-02', '2020-01-10']
     * return None
     */
    public static function filterRangeTanggalWith2Column(&$query, $kolomFilter = [], $rangeFilter = [])
    {
        if (count($kolomFilter) != 2 || count($rangeFilter) != 2) {
            return;
        }

        $kolomMin = $kolomFilter[0];
        $kolomMax = $kolomFilter[1];

        $rangeMin = \Carbon\Carbon::parse($rangeFilter[0])->toDateString();
        $rangeMax = \Carbon\Carbon::parse($rangeFilter[1])->toDateString();

        if (strlen($rangeMin) > 0 && strlen($rangeMax) > 0) {
            $period = \Carbon\CarbonPeriod::create($rangeMin, $rangeMax);
            $periodeBetween = [];
            foreach ($period as $date) {
                $periodeBetween[] = $date->format('Y-m-d');
            }

            $query->where(function ($query) use ($rangeMin, $rangeMax, $periodeBetween, $kolomMax, $kolomMin) {
                $query->whereIn($kolomMax, $periodeBetween);
                $query->orWhereIn($kolomMin, $periodeBetween);
                $query->orWhere(function ($sql) use ($rangeMin, $rangeMax, $kolomMax, $kolomMin) {
                    $sql->where($kolomMin, '<', $rangeMin);
                    $sql->where($kolomMax, '>', $rangeMax);
                });
            });
        } elseif ((strlen($rangeMin) > 0 && strlen($rangeMax) == 0) || (strlen($rangeMin) == 0 && strlen($rangeMax) > 0)) {
            if (strlen($rangeMin) > 0) {
                $query->where($kolomMin, '>=', $rangeMin);
            }

            if (strlen($rangeMax) > 0) {
                $query->where($kolomMax, '<=', $rangeMax);
            }
        }

        return;
    }

    /**
     * $query => query data objek
     * $kolInput => nama kolom yang isinya nama dari file yang diupload
     * $fileObj => object file yang diupload
     * $typeFile => array type file yang diupload untuk validasi, contoh ['image', 'document, 'video']
     * return ResponseHelper
     */
    public static function uploadAndSetFileToTable(&$query, $kolInput, $fileObj = null, $typeFile = [])
    {
        if (!empty($fileObj)) {
            $isUploadFile = FileValidationHelper::isDoUploadFile($fileObj);

            if ($isUploadFile) {
                $invalidTypeCount = 0;
                foreach ($typeFile as $type) {
                    $validasiFileObj = FileValidationHelper::validateFile($type, $fileObj);
                    if ($validasiFileObj['code'] != 200) {
                        $invalidTypeCount++;
                    }
                }

                if ($invalidTypeCount == count($typeFile)) {
                    return $validasiFileObj;
                }

                $fileName = date("YmdHis") . base64_encode($fileObj->getClientOriginalName()) . "." . $fileObj->getClientOriginalExtension();
                $uploadResult = UploadFileHelper::uploadFile($fileName, $fileObj);
                if (!$uploadResult) {
                    return ResponseHelper::errorResponse(400, MessageHelper::errorUpload());
                }

                $query->{$kolInput} = $fileName;
            } else {
                $query->{$kolInput} = is_string($fileObj) ? $fileObj : null;
            }
        }

        return ResponseHelper::successResponse(MessageHelper::successStored($kolInput), []);
    }
}
