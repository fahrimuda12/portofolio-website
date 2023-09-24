<?php

namespace App\Models;

class StringPrimaryKeyModel extends BaseModel
{
    public $incrementing = false;
    protected $keyType = 'string';
}
