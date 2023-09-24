<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\TimezoneHelper;
use Carbon\Carbon;

class BaseModel extends Model
{
    public $timestamps = false;

    public function scopePakaiKoneksi($query, $conn)
    {
        $class = (new static);
        $class->setConnection($conn);
        $query = $class;
        return $query;
    }
}
