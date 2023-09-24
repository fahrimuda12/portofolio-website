<?php

namespace App\Models\UserManagement;

use App\DomainModels\UserDomainModel;
use App\Helpers\UploadFileHelper;
use App\Models\HiddenTimestampModel;
use App\Models\UserManagement\ModulModel;
use App\Models\UserManagement\FiturModel;

class UserModel extends HiddenTimestampModel
{
    protected $table = 'user';
    protected $primaryKey = 'user_id';
    public ?UserDomainModel $userDomainModel = null;

    public function userDomain()
    {
        if (is_null($this->userDomainModel)) {
            $this->userDomainModel = new UserDomainModel($this);
        }

        return $this->userDomainModel;
    }

    public function userGroupRelation()
    {
        return $this->belongsTo(UserGroupModel::class, 'user_group_id', 'user_group_id');
    }

    public function bahasaRelation()
    {
        return $this->belongsTo(\App\Models\Main\BahasaModel::class, 'bahasa_id_aktif', 'bahasa_id');
    }

    public function hakAksesUserRelation()
    {
        return $this->hasMany(HakAksesUserModel::class, 'user_id', 'user_id')->whereNull('dihapus_pada');
    }

    public function treeHakAksesUser()
    {
        $fiturHakAksesId = self::hakAksesUserRelation()->pluck('fitur_id')->toArray();

        $baseUrlFitur = FiturModel::query()
            ->whereIn('fitur_id', $fiturHakAksesId);

        $modulHakAkses = (clone ($baseUrlFitur))
            ->distinct()
            ->get(['modul_id']);

        $returnArray = [];
        foreach ($modulHakAkses as $key => $value) {
            $returnArray[$key]['modul_id'] = $value->modul_id;
            $returnArray[$key]['modul'] = $value->modul->nama ?? "-";
            // dd($value->modul->nama);
            $returnArray[$key]['fitur'] = (clone ($baseUrlFitur))->selectRaw("fitur_id, modul_id, nama as fitur")->where('modul_id', $value->modul_id)->get()->toArray();
        }

        return $returnArray;
    }

    public function hakAksesUserModulFitur()
    {
        $fiturHakAkses = self::hakAksesUserRelation()->get();
        $fiturIdTerlibat = $fiturHakAkses->pluck('fitur_id')->toArray();

        $modulIdTerlibat = FiturModel::query()
            ->whereIn('fitur_id', $fiturIdTerlibat)
            ->distinct()
            ->pluck('modul_id')
            ->toArray();

        return [
            'modul' => implode(';', $modulIdTerlibat),
            'fitur' => implode(';', $fiturIdTerlibat),
        ];
    }

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
}
