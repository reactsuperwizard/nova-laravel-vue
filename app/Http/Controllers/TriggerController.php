<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use App\WaitingProcess;

use Log;

class TriggerController extends Controller
{
    //

    /*
	 * Check status of processing
	 * return 1 when process is running
    */
    public function checkProcessingStatus()
    {
    	$status = Status::find(1);
    	Log::info($status);
    	return $status;
    }

    public function startProcessing()
    {
    	
    	$status = $this->checkProcessingStatus();
    	// Check status of processing
    	if ($status) {
    		return;
    	}

    	// Pick one file
    	$filename = WaitingProcess::where('active', '1')->oldest();


    }
}
