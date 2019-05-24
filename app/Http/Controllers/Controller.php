<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function saveFile($_file){
		
		set_time_limit(0);

		$dir = '/app/secure/'; 
		$filename = md5(microtime()) .'.csv';
	 	$target_file = $dir.$filename;

		$target_file = storage_path($target_file);
		
		$_filename = $_file['name'];
		
		$ext = pathinfo($_filename, PATHINFO_EXTENSION);
		
		
		$inputFileName = $_file["tmp_name"];

		if(($ext=='csv' || $ext='txt') && move_uploaded_file($inputFileName, $target_file)){

			return ['name'=>$_filename ,'filename'=> $filename,'target_file'=>$target_file];
			
		}
		
				
		return false;
	}


	public function clone($filepath)
	{


		$filepath =  str_replace('.csv','', $filepath);
		// open the "save-file"
		if (($saveHandle = fopen($filepath."_purify.csv", "w")) !== false) {
		    // open the csv file
		    if (($readHandle = fopen($filepath.".csv", "r")) !== false) {
		        // read each line into an array
		        while (($data = fgetcsv($readHandle, 8192, ",")) !== false) {


		            // write this line to the file
		            fputcsv($saveHandle, $data);
		        }
		        fclose($readHandle);
		    }
		    fclose($saveHandle);
		}

		return true;
	}
}
