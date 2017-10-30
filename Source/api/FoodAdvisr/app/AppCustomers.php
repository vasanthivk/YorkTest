<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppCustomers extends Model
{
    public $timestamps = false;
    protected $table = 'appusers';
}
