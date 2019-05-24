<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //
    protected $table = "processing_status";
    public $timestamps = false;

    public function getStatus()
    {
    	return $this->status;
    }

}
