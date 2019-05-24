<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    public $connection = 'mysql';
    public $primaryKey = 'id';
    public $table = 'mappings';
    public $fillable = array (
        'csvHeading',
        'field'
    );

    public $timestamps = true;
}
