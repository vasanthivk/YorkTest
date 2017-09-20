<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Defaults extends Model
{
    public $timestamps = false;
    protected $table = 'defaults';
}
