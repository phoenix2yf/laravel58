<?php

namespace App\Models\Capture;

use Illuminate\Database\Eloquent\Model;
use DB;

class BaseModel extends Model
{
    protected $connection = 'capture';
    public $timestamps = false;
}
