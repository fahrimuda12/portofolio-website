<?php

namespace App\Repositories\Main;

use App\Helpers\ChecksumHelper;
use App\Helpers\LogActivityHelper;
use App\Helpers\MessageHelper;
use App\Helpers\ResponseHelper;
use App\Models\Main\ProjectModel;
use App\Resources\Attribut\DetailAttributResource;
use App\Resources\Attribut\TreeAttributResource;
use App\Resources\Project\DaftarProjectResource;
use App\Resources\Project\DetailProjectResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectRepository
{
    const PANJANG_WILAYAH_PARENT = 3;

     /**
     * @param array $params parameter dari controller,
     * params valid: ?cari, ?page, ?limit
     * @return array ResponseHelper
     */
    public function aksiGetSemua($params, $isWithPaginasi = true)
    {
        try {
            $page = isset($params['page']) ? intval($params['page']) : 1;
            $limit = isset($params['limit']) ? intval($params['limit']) : 10;
            $cari = isset($params['cari']) ? $params['cari'] : '';

            $data = ProjectModel::query()
                ->select('*')
                ->selectRaw("ROW_NUMBER() over(ORDER BY dibuat_pada DESC) no_urut")
                ->orderBy('dibuat_pada', "DESC");

            if ($isWithPaginasi) {
                $count = ($data)->count();
                if ($limit > 0) {
                    $data->take($limit);
                }

                if ($page > 0) {
                    $data->skip(($page - 1) * $limit);
                }

                $data = $data->get();

                $returnData = [
                    'data' => new DaftarProjectResource($data),
                    'total_data' => $count,
                    'per_halaman' => $limit,
                    'halaman_sekarang' => $page,
                    'total_halaman' => ceil($count / $limit),
                ];
            } else {
                $data = $data->get();
                $returnData = new DaftarProjectResource($data);
            }
            return ResponseHelper::successResponse(MessageHelper::successFound('project'), $returnData);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    /**
     * @param array $params parameter dari controller,
     * params valid: *attribut_id
     * @return array ResponseHelper
     */
    public function aksiGetDetail($params)
    {
        try {
            $projectId = isset($params['project_id']) ? $params['project_id'] : null;
            if (empty($projectId)) {
                return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('project_id'));
            }

            $data = ProjectModel::query()
                ->whereNull('dihapus_pada')
                ->find($projectId);

            if (is_null($data)) {
                return ResponseHelper::errorResponse(400, MessageHelper::errorNotFound('project'));
            }

            $returnData = new DetailProjectResource($data);

            return ResponseHelper::successResponse(MessageHelper::successFound('project'), $returnData);
        } catch (\Exception $exception) {
            return ResponseHelper::serverErrorResponse($exception);
        }
    }

    // /**
    //  * @param array $params parameter dari controller,
    //  * params valid: ?project_id, ?parent_project_id. *nama, *longitude, *latitude, *alamat, *nomor_telepon,
    //  * *grafik_data_source_id, *table_data_source_id, *list_data_source_id, *wilayah_id
    //  * @return array ResponseHelper
    //  */
    // public function aksiSimpan($params){
    //     DB::beginTransaction();
    //     try {
    //         $activeUser = active_user($params);
    //         if (is_null($activeUser)) {
    //             return ResponseHelper::errorResponse(401, MessageHelper::unauthorized());
    //         }
    //         $parentprojectId = isset($params['parent_project_id']) ? $params['parent_project_id'] : '';
    //         $projectId = isset($params['project_id']) ? $params['project_id'] : '';
    //         $isUpdate = strlen($projectId) > 0;

    //         $nama = isset($params['nama']) ? $params['nama'] : '';
    //         if (strlen($nama) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('nama'));
    //         }

    //         $longitude = isset($params['longitude']) ? $params['longitude'] : '';
    //         if (strlen($longitude) == 0 && strlen($projectId) == 9) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('longitude'));
    //         }

    //         $latitude = isset($params['latitude']) ? $params['latitude'] : '';
    //         if (strlen($latitude) == 0 && strlen($projectId) == 9) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('is aktif'));
    //         }

    //         $alamat = isset($params['alamat']) ? $params['alamat'] : '';
    //         if (strlen($alamat) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('alamat'));
    //         }

    //         $noTelepon = isset($params['nomor_telepon']) ? $params['nomor_telepon'] : '';
    //         if (strlen($noTelepon) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('nomor_telepon'));
    //         }

    //         $grafikDataSourceId = isset($params['grafik_data_source_id']) ? $params['grafik_data_source_id'] : '';
    //         if (strlen($grafikDataSourceId) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('grafik_data_source_id'));
    //         }

    //         $tableDataSourceId = isset($params['table_data_source_id']) ? $params['table_data_source_id'] : '';
    //         if (strlen($tableDataSourceId) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('table_data_source_id'));
    //         }

    //         $listDataSourceId = isset($params['list_data_source_id']) ? $params['list_data_source_id'] : '';
    //         if (strlen($listDataSourceId) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('list_data_source_id'));
    //         }

    //         $wilayahId = isset($params['wilayahId']) ? $params['wilayahId'] : '';
    //         if (strlen($wilayahId) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('wilayah id'));
    //         }

    //         $data = $isUpdate ? ProjectModel::query()->find($projectId) : new ProjectModel();
    //         if ($isUpdate && is_null($data)) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorNotFound('user'));
    //         }

    //         $data = $isUpdate ? ProjectModel::find($projectId) : new ProjectModel();
    //         $data->attribut_id = $isUpdate ? $data->attribut_id : $this->_generateKode($parentprojectId);
    //         $data->nama = $nama;
    //         $data->longitude = $longitude;
    //         $data->latitude = $latitude;
    //         $data->alamat = $alamat;
    //         $data->nomor_telepon = $noTelepon;
    //         $data->grafik_data_source_id = $grafikDataSourceId;
    //         $data->table_data_source_id = $tableDataSourceId;
    //         $data->list_data_source_id = $listDataSourceId;
    //         $data->wilayah_id = $wilayahId;
    //         $data->dibuat_pada = $isUpdate ? $data->dibuat_pada : Carbon::now();
    //         $data->dibuat_oleh_user_id = $isUpdate ? $data->dibuat_oleh : $activeUser->user_id;
    //         $data->diubah_pada = !$isUpdate ? $data->diubah_pada : Carbon::now();
    //         $data->diubah_oleh_user_id = !$isUpdate  ? $data->diubah_oleh_user_id : $activeUser->user_id;
    //         $data->save();

    //         //Insert checksum
    //         $model = new ProjectModel();
    //         $refineChecksum = ChecksumHelper::refineChecksum($model->getTable(), $model->getKeyName(), $data[$model->getKeyName()]);
    //         if ($refineChecksum['code'] != 200) {
    //             DB::rollBack();
    //             return $refineChecksum;
    //         }

    //         // insertLog
    //         $log = LogActivityHelper::simpanLog($activeUser, ($isUpdate ? 'ubah' : 'tambah') . '-attribut', [
    //             'attribut_id' => $data->attribut_id
    //         ]);
    //         if ($log['code'] != 200) {
    //             DB::rollBack();
    //             return $log;
    //         }

    //         DB::commit();
    //         return ResponseHelper::successResponse(MessageHelper::successStored('project'), $data);
    //     } catch (\Exception $exception) {
    //         DB::rollBack();
    //         return ResponseHelper::serverErrorResponse($exception);
    //     }
    // }

    // /**
    //  * @param array $params parameter dari controller,
    //  * params valid: *attribut_id
    //  * @return array ResponseHelper
    //  */
    // public function aksiHapus($params)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $activeUser = active_user($params);

    //         $projectId = isset($params['attribut_id']) ? $params['attribut_id'] : '';
    //         if (strlen($projectId) == 0) {
    //             return ResponseHelper::errorResponse(400, MessageHelper::errorRequired('attribut_id'));
    //         }

    //         $data = ProjectModel::query()->find($projectId);
    //         if (is_null($data)) {
    //             return ResponseHelper::errorResponse(404, MessageHelper::errorNotFound('attribut_id'));
    //         }
    //         $data->dihapus_pada = Carbon::now();
    //         $data->dihapus_oleh_user_id = $activeUser->user_id;
    //         $data->save();

    //         //Insert checksum
    //         $model = new ProjectModel();
    //         $refineChecksum = ChecksumHelper::refineChecksum($model->getTable(), $model->getKeyName(), $data[$model->getKeyName()]);
    //         if ($refineChecksum['code'] != 200) {
    //             DB::rollBack();
    //             return $refineChecksum;
    //         }

    //         // insertLog
    //         $log = LogActivityHelper::simpanLog($activeUser, 'hapus-attribut', [
    //             'attribut_id' => $data->attribut_id,
    //             'user_id' => $activeUser->user_id
    //         ]);
    //         if ($log['code'] != 200) {
    //             DB::rollBack();
    //             return $log;
    //         }

    //         DB::commit();
    //         return ResponseHelper::successResponse(MessageHelper::successRemoved('project'), []);
    //     } catch (\Exception $exception) {
    //         DB::rollBack();
    //         return ResponseHelper::serverErrorResponse($exception);
    //     }
    // }
}
