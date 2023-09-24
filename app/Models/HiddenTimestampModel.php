<?php

namespace App\Models;

class HiddenTimestampModel extends BaseModel
{
    protected $hidden = ['diubah_pada', 'diubah_oleh_user_id', 'dihapus_pada', 'dihapus_oleh_user_id', 'checksum_id'];

    public function userPembuat()
    {
        return $this->hasOne('App\Models\UserManagement\UserModel', 'user_id', 'dibuat_oleh_user_id');
    }
}
