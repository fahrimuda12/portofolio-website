<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChecksumHelper
{
    const TABLE_TIDAK_VALID = 'Table tidak valid!';
    const DATA_TIDAK_DITEMUKAN = 'Data tidak ditemukan!';
    const DATA_BERHASIL_DISIMPAN = 'Data berhasil disimpan!';
    const CHECKSUM_TIDAK_VALID = 'Checksum tidak valid!';
    const CHECKSUM_VALID = 'Checksum valid!';

    public static function refineChecksum($tableName, $primaryKeyFieldName, $uniqueId, $connection = null)
    {
        try {
            $connection = self::getConnection($connection);

            $activeTable = Schema::connection($connection)->getColumnListing($tableName);
            if (count($activeTable) == 0) {
                return ResponseHelper::errorResponse(400, self::TABLE_TIDAK_VALID);
            }

            if (is_array($uniqueId)) {
                foreach ($uniqueId as $id) {
                    $currentData = DB::connection($connection)->table($tableName)
                        ->where($primaryKeyFieldName, '=', $id)
                        ->first();
                    if (is_null($currentData)) {
                        return ResponseHelper::errorResponse(400, self::DATA_TIDAK_DITEMUKAN + "checksum helper");
                    }

                    $cheksumData = "";
                    foreach ($activeTable as $field) {
                        if ($field == 'checksum_id') {
                            continue;
                        }

                        $cheksumData .= $currentData->$field;
                    }

                    $cheksumData = md5($cheksumData);
                    DB::connection($connection)->table($tableName)
                        ->where($primaryKeyFieldName, '=', $id)
                        ->update([
                            'checksum_id' => $cheksumData
                        ]);
                }
            } else {
                $currentData = DB::connection($connection)->table($tableName)
                    ->where($primaryKeyFieldName, '=', $uniqueId)
                    ->first();

                if (is_null($currentData)) {
                    return ResponseHelper::errorResponse(400, self::DATA_TIDAK_DITEMUKAN);
                }

                $cheksumData = "";
                foreach ($activeTable as $field) {
                    if ($field == 'checksum_id') {
                        continue;
                    }

                    $cheksumData .= $currentData->$field;
                }

                $cheksumData = md5($cheksumData);
                DB::connection($connection)->table($tableName)
                    ->where($primaryKeyFieldName, '=', $uniqueId)
                    ->update([
                        'checksum_id' => $cheksumData
                    ]);
            }

            return ResponseHelper::successResponse(self::DATA_BERHASIL_DISIMPAN, null);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    public static function validateChecksum($tableName, $primaryKeyFieldName, $uniqueId)
    {
        try {
            $connection = self::getConnection($connection);

            $activeTable = Schema::connection($connection)->getColumnListing($tableName);
            if (count($activeTable) == 0) {
                return ResponseHelper::errorResponse(400, self::TABLE_TIDAK_VALID);
            }

            $currentData = DB::connection($connection)->table($tableName)
                ->where($primaryKeyFieldName, '=', $uniqueId)
                ->first();

            if (is_null($currentData)) {
                return ResponseHelper::errorResponse(400, self::DATA_TIDAK_DITEMUKAN);
            }

            $cheksumData = "";
            foreach ($activeTable as $field) {
                if ($field == 'checksum_id') {
                    continue;
                }

                $cheksumData .= $currentData->$field;
            }

            $cheksumData = md5($cheksumData);
            if ($currentData->checksum_id != $cheksumData) {
                return ResponseHelper::errorResponse(400, self::CHECKSUM_TIDAK_VALID);
            }

            return ResponseHelper::successResponse(self::CHECKSUM_VALID, null);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    private static function getConnection($connection)
    {
        return is_null($connection) ? config('database.default') : $connection;
    }
}
