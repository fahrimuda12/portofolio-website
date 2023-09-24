<?php

namespace App\Helpers;

use App\Resources\CrudGenerator\DaftarResource;
use App\Resources\CrudGenerator\DetailResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrudGeneratorHelper
{
    const DEFAULT_ORDER_KOLOM = 'nama';
    const KOLOM_FILE_KEYWORD = ['logo', 'file', 'foto'];
    const EXCEPT_SEARCH_RELATION = ['profile_keanggotaan_id'];
    const EXCEPT_FILE_KEYWORD = ['profile_keanggotaan_id'];

    public static function generateCrud($params = [])
    {
        try {
            $aksi = $params['aksi'] ?? null;

            switch ($aksi) {
                case 'r':
                    $result = self::_read($params);
                    break;
                case 's':
                    $result = self::_save($params);
                    break;
                case 'd':
                    $result = self::_delete($params);
                    break;
                case 'a':
                    $result = self::_aktif($params);
                    break;
                default:
                    $result = ResponseHelper::errorResponse(400, MessageHelper::errorInvalid('aksi'));
                    break;
            }

            return $result;
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    private static function _read($params = [])
    {
        try {
            $model = $params['model'] ?? null;
            $id = $params['id'] ?? null;
            $cari = $params['cari'] ?? null;
            $limit = $params['limit'] ?? 10;
            $page = $params['page'] ?? 1;
            $where = $params['where'] ?? [];
            $join = $params['join'] ?? [];
            $order = $params['order'] ?? [];
            $showAllRelation = $params['show_all_relation'] ?? 0;

            $isWithPaginasi = is_null($id);

            $table = (new $model)->getTable();
            $pk = (new $model)->getKeyName();

            DdlHelper::$isWithPk = 1;
            DdlHelper::$isWithTimestamp = 1;

            $allColumns = DdlHelper::getAllTableColumn($table);
            $withoutKolom = array_merge([DdlHelper::STRING_KOLOM_CHECKSUM], array_column(DdlHelper::KOLOM_TIMESTAMP, 'nama'));

            $kolomTable = array_values(array_filter($allColumns, fn ($item) => !in_array($item, $withoutKolom)));
            $kolomTable = array_map(function ($item) use ($table) {
                return $table.'.'.$item;
            }, $kolomTable);
            $kolomPencarian = array_values(array_filter($kolomTable, fn ($item) => !in_array($item, array_merge([$pk], self::EXCEPT_SEARCH_RELATION))));

            $selectRaw = implode(',', $kolomTable);

            $query = (new $model)->query();

            if (count($join) > 0) {
                foreach ($join as $tabelJoin => $onKolom) {
                    $explodeOn = clean_explode($onKolom, '=');
                    if (count($explodeOn) != 2) {
                        continue;
                    }

                    $query->leftJoin($tabelJoin, $explodeOn[0], '=', $explodeOn[1]);
                }
            }

            $orderBy = count($order) > 0 ? implode(',', $order) : (in_array(self::DEFAULT_ORDER_KOLOM, $allColumns) ? $table.'.'.self::DEFAULT_ORDER_KOLOM . ' asc' : $table.'.'.$pk . ' asc');
            
            $query->selectRaw("$selectRaw, ROW_NUMBER() over(ORDER BY $orderBy) no_urut");

            if (in_array(DdlHelper::STRING_KOLOM_DIHAPUS, $allColumns)) {
                $query->whereNull($table.'.'.DdlHelper::STRING_KOLOM_DIHAPUS);
            }

            if (!is_null($id)) {
                $query->where($table.'.'.$pk, $id);
            }

            if (count($where) > 0) {
                $query->where($where);
            }

            if (!is_null($cari)) {
                $query->where(function ($query) use ($kolomPencarian, $cari) {
                    $model = $query->getModel();
                    foreach ($kolomPencarian as $tableKolom) {
                        $explodedTableKolom = clean_explode($tableKolom, '.');
                        $table = $explodedTableKolom[0];
                        $kolom = $explodedTableKolom[1];
                        $explodedSegment = clean_explode($kolom, '_');
                        $idxId = array_search('id', $explodedSegment);

                        if (strpos($kolom, '_id') > -1 && substr($kolom, -3) != '_id') {
                            $temp = $explodedSegment[$idxId];
                            $explodedSegment[$idxId] = $explodedSegment[($idxId + 1)];
                            $explodedSegment[($idxId + 1)] = $temp;

                            $kolom = implode('_', $explodedSegment);
                        }

                        if (substr($kolom, -3) == '_id') {
                            // pencarian by relasi belum bisa
                            
                            // DdlHelper::$isWithPk = 0;
                            // DdlHelper::$isWithTimestamp = 0;

                            // $allColumns = array_filter(DdlHelper::getAllTableColumn($table), fn ($item) => $item != DdlHelper::STRING_KOLOM_CHECKSUM);

                            // $relation = implode('', array_map('ucfirst', clean_explode(str_replace('_id', '_relation', $kolom), '_')));
                            // $relation = strtolower(substr($relation, 0, 1)) . substr($relation, 1);

                            // $query->orWhereHas($relation, function ($query) use ($cari, $allColumns) {
                            //     foreach ($allColumns as $kols) {
                            //         $query->orWhere($kols, 'ILIKE', '%' . $cari . '%');
                            //     }
                            // });
                        } else {
                            $query->orWhere($table.'.'.$kolom, 'ILIKE', '%' . $cari . '%');
                        }
                    }
                });
            }

            if (intval($showAllRelation) == 1) {
                DaftarResource::$removeExceptRelation = 1;
                DetailResource::$removeExceptRelation = 1;
            }

            if ($isWithPaginasi) {
                $total = (clone ($query))->count();

                if ($page > 0) {
                    $query->skip(($page - 1) * $limit);
                }

                if ($limit > 0) {
                    $query->take($limit);
                }

                $data = $query->orderByRaw($orderBy)->get();

                $result = [
                    'data' => new DaftarResource($data),
                    'total_data' => $total,
                    'total_halaman' => ($total > 0) ? ceil($total / $limit) : 1,
                    'halaman_sekarang' => $page,
                    'per_halaman' => $limit,
                ];
            } else {
                $data = $query->first();
                if (is_null($data)) {
                    return ResponseHelper::successResponse(404, MessageHelper::errorNotFound($table));
                }

                $result = new DetailResource($data);
            }

            DaftarResource::$removeExceptRelation = 0;
            DetailResource::$removeExceptRelation = 0;

            return ResponseHelper::successResponse(MessageHelper::successFound($table), $result);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    private static function _save($params = [])
    {
        DB::beginTransaction();
        try {
            $model = $params['model'] ?? null;
            $body = $params['body'] ?? [];
            $required = $params['required'] ?? [];
            $activeUser = $params['activeUser'] ?? null;
            $isWithoutUploadFile = $params['is_without_upload_file'] ?? 0;
            $returnId = $params['returnId'] ?? 0;
            $isUpsert = $params['isUpsert'] ?? 0;

            $table = (new $model)->getTable();
            $pk = (new $model)->getKeyName();

            $typeDataPk = DdlHelper::getColumnDataType($table, $pk);

            DdlHelper::$isWithPk = 1;
            DdlHelper::$isWithTimestamp = 1;

            $allColumns = DdlHelper::getAllTableColumn($table);
            $withoutKolom = array_merge([DdlHelper::STRING_KOLOM_CHECKSUM], array_column(DdlHelper::KOLOM_TIMESTAMP, 'nama'));

            $kolomTable = array_values(array_filter($allColumns, fn ($item) => !in_array($item, $withoutKolom)));

            $isPkInBody = in_array($pk, array_keys($body));
            $isUbah = ($isPkInBody && strlen(strval($body[$pk])) > 0);

            if ($isUpsert) {
                $prevData = (new $model)->query()->where($body);
                if (in_array(DdlHelper::STRING_KOLOM_DIHAPUS, $allColumns)) {
                    $prevData->whereNull(DdlHelper::STRING_KOLOM_DIHAPUS);
                }
                $prevData = $prevData->first();
                $data = is_null($prevData) ? (new $model) : $prevData;
            } else {
                $data = $isUbah ? (new $model)->query()->find($body[$pk]) : (new $model);
            }

            if ($isUbah && is_null($data)) {
                return ResponseHelper::errorResponse(400, MessageHelper::errorNotFound($table));
            }

            if (strpos($typeDataPk, 'int') === false) {
                $data->{$pk} = $isUbah ? $data->{$pk} : UuidHelper::prepareUuid();
            }

            $body = array_filter($body, fn ($item) => $item != $pk, ARRAY_FILTER_USE_KEY);


            foreach ($body as $kol => $val) {
                if (!in_array($kol, $kolomTable)) {
                    continue;
                }

                if (in_array($kol, $required) && strlen(strval($val)) == 0) {
                    DB::rollBack();
                    return ResponseHelper::errorResponse(400, MessageHelper::errorRequired(str_replace('_', ' ', $kol)));
                }

                $isFileFormat = false;
                if (!in_array($kol, self::EXCEPT_FILE_KEYWORD) && !$isWithoutUploadFile) {
                    foreach (self::KOLOM_FILE_KEYWORD as $kolFile) {
                        $isFileFormat = strpos($kol, $kolFile) > -1;
                        if ($isFileFormat) {
                            break;
                        }
                    }
                }

                $isUploadFile = FileValidationHelper::isDoUploadFile($val);

                if ($isFileFormat && $isUploadFile) {
                    $file = $val;
                    if (!empty($file)) {
                        $validasiFileFoto = FileValidationHelper::validateFile('image', $file);
                        $validasiFileDokumen = FileValidationHelper::validateFile('document', $file);
                        if (!($validasiFileFoto['code'] == 200 || $validasiFileDokumen['code'] == 200)) {
                            DB::rollBack();
                            return $validasiFileFoto['code'] != 200 ? $validasiFileFoto : $validasiFileDokumen;
                        }

                        $fileName = date("YmdHis") . base64_encode($file->getClientOriginalName()) . "." . $file->getClientOriginalExtension();
                        $uploadResult = UploadFileHelper::uploadFile($fileName, $file);
                        if (!$uploadResult) {
                            DB::rollBack();
                            return ResponseHelper::errorResponse(400, MessageHelper::errorUpload());
                        }

                        $data->{$kol} = $fileName;
                    }
                } elseif (!$isFileFormat) {
                    $data->{$kol} = $val;
                }
            }


            if (in_array(DdlHelper::STRING_KOLOM_DIBUAT, $allColumns)) {
                if (is_null($activeUser)) {
                    DB::rollBack();
                    return ResponseHelper::errorResponse(401, MessageHelper::unauthorized());
                }

                $data->dibuat_pada = $isUbah ? $data->dibuat_pada : Carbon::now();
                $data->dibuat_oleh_user_id = $isUbah ? $data->dibuat_oleh_user_id : $activeUser->user_id;
                $data->diubah_pada = !$isUbah ? $data->diubah_pada : Carbon::now();
                $data->diubah_oleh_user_id = !$isUbah ? $data->diubah_oleh_user_id : $activeUser->user_id;
            }

            $data->save();


            //Insert checksum
            if (in_array(DdlHelper::STRING_KOLOM_CHECKSUM, $allColumns)) {
                $refineChecksum = ChecksumHelper::refineChecksum($table, $pk, $data[$pk]);
                if ($refineChecksum['code'] != 200) {
                    DB::rollBack();
                    return $refineChecksum;
                }
            }


            // insertLog
            $logName = str_replace('_', '-', $table);
            $log = LogActivityHelper::simpanLog($activeUser, ($isUbah ? 'ubah' : 'tambah') . '-' . $logName, $body);
            if ($log['code'] != 200) {
                DB::rollBack();
                return $log;
            }


            DB::commit();
            $responseName = str_replace('_', ' ', $table);
            return ResponseHelper::successResponse(MessageHelper::successStored($responseName), ($returnId ? [$pk => $data->{$pk}] : []));
        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    private static function _delete($params = [])
    {
        DB::beginTransaction();
        try {
            $model = $params['model'] ?? null;
            $id = $params['id'] ?? null;
            $activeUser = $params['activeUser'] ?? null;
            $returnId = $params['returnId'] ?? 0;

            $table = (new $model)->getTable();
            $pk = (new $model)->getKeyName();

            DdlHelper::$isWithPk = 1;
            DdlHelper::$isWithTimestamp = 1;

            $allColumns = DdlHelper::getAllTableColumn($table);
            $withoutKolom = array_merge([DdlHelper::STRING_KOLOM_CHECKSUM], array_column(DdlHelper::KOLOM_TIMESTAMP, 'nama'));

            $kolomTable = array_values(array_filter($allColumns, fn ($item) => !in_array($item, $withoutKolom)));

            $data = (new $model)->query()->find($id);
            if (is_null($data)) {
                return ResponseHelper::errorResponse(400, MessageHelper::errorNotFound($table));
            }

            if (in_array(DdlHelper::STRING_KOLOM_DIHAPUS, $allColumns)) {
                if (is_null($activeUser)) {
                    return ResponseHelper::errorResponse(401, MessageHelper::unauthorized());
                }

                $data->dihapus_pada = Carbon::now();
                $data->dihapus_oleh_user_id = $activeUser->user_id;

                $data->save();
            } else {
                $data->delete();
            }


            //Insert checksum
            if (in_array(DdlHelper::STRING_KOLOM_CHECKSUM, $allColumns)) {
                $refineChecksum = ChecksumHelper::refineChecksum($table, $pk, $data[$pk]);
                if ($refineChecksum['code'] != 200) {
                    DB::rollBack();
                    return $refineChecksum;
                }
            }


            // insertLog
            $logName = str_replace('_', '-', $table);
            $log = LogActivityHelper::simpanLog($activeUser, 'hapus-' . $logName, [$pk => $id]);
            if ($log['code'] != 200) {
                DB::rollBack();
                return $log;
            }

            DB::commit();
            $responseName = str_replace('_', ' ', $table);
            return ResponseHelper::successResponse(MessageHelper::successRemoved($responseName), ($returnId ? [$pk => $id] : []));
        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    private static function _aktif($params = [])
    {
        DB::beginTransaction();
        try {
            $model = $params['model'] ?? null;
            $id = $params['id'] ?? null;
            $body = $params['body'] ?? [];
            $activeUser = $params['activeUser'] ?? null;

            $table = (new $model)->getTable();
            $pk = (new $model)->getKeyName();

            DdlHelper::$isWithPk = 1;
            DdlHelper::$isWithTimestamp = 1;

            $allColumns = DdlHelper::getAllTableColumn($table);
            $withoutKolom = array_merge([DdlHelper::STRING_KOLOM_CHECKSUM], array_column(DdlHelper::KOLOM_TIMESTAMP, 'nama'));

            $kolomTable = array_values(array_filter($allColumns, fn ($item) => !in_array($item, $withoutKolom)));

            $data = (new $model)->query()->find($id);
            if (is_null($data)) {
                return ResponseHelper::errorResponse(400, MessageHelper::errorNotFound($table));
            }

            if (in_array('is_aktif', $allColumns)) {
                $data->is_aktif = intval($body['is_aktif']);
            }

            if (in_array('is_active', $allColumns)) {
                $data->is_active = intval($body['is_aktif']);
            }

            if (in_array('is_available', $allColumns)) {
                $data->is_available = intval($body['is_aktif']);
            }

            if (in_array(DdlHelper::STRING_KOLOM_DIUBAH, $allColumns)) {
                if (is_null($activeUser)) {
                    return ResponseHelper::errorResponse(401, MessageHelper::unauthorized());
                }

                $data->diubah_pada = Carbon::now();
                $data->diubah_oleh_user_id = $activeUser->user_id;
            }

            $data->save();


            //Insert checksum
            if (in_array(DdlHelper::STRING_KOLOM_CHECKSUM, $allColumns)) {
                $refineChecksum = ChecksumHelper::refineChecksum($table, $pk, $data[$pk]);
                if ($refineChecksum['code'] != 200) {
                    DB::rollBack();
                    return $refineChecksum;
                }
            }


            // insertLog
            $logName = str_replace('_', '-', $table);
            $log = LogActivityHelper::simpanLog($activeUser, 'set-aktif-' . $logName, [$pk => $id]);
            if ($log['code'] != 200) {
                DB::rollBack();
                return $log;
            }

            DB::commit();
            $responseName = str_replace('_', ' ', $table);
            return ResponseHelper::successResponse(MessageHelper::successStored($responseName), []);
        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponseHelper::serverErrorResponse($exception);
        }
    }
}
