<?php

namespace App\Models;

class StringPrimaryKeyHiddenTimestampModel extends StringPrimaryKeyModel
{
    protected $hidden = ['diubah_oleh_user_id', 'dihapus_pada', 'dihapus_oleh_user_id', 'checksum_id'];

    public function userPembuat()
    {
        return $this->hasOne('App\Models\UserManagement\UserModel', 'user_id', 'dibuat_oleh_user_id');
    }
}
