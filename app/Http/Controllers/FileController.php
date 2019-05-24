<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\WaitingProcess;
use Log;

class FileController extends Controller
{
    function index()
    {
        return view('upload');
    }

    function upload(Request $request)
    {

        $convert = new ConverterController();

        if(isset($_FILES["fileToUpload"])){

            // Validate file extension
            $mimetype = mime_content_type($_FILES["fileToUpload"]["tmp_name"]);


            if(!in_array($mimetype, array('text/plain')) ) {
               echo '{"success":false,"error":"Invalid File Type"}';
               exit();
            }
            $info = $this->saveFile($_FILES["fileToUpload"]);
        }
        if ($info) {
            // Extract information from $info array
            @extract($info);
            $path = $target_file;
            $path = 'secure/'.$filename;


            // $result = $convert->convertOne($path);

            // Create waiting process
            $waiting_process = new WaitingProcess();
            $waiting_process->filename = $path;
            $waiting_process->active = 1;
            $waiting_process->save();

            return back()->with('success', 'File Uploaded Successfully')->with($path, $filename);
            // return Storage::download($path);
        }else{
            return back()->with('failed', 'File Upload/Conversion Failed')->with($path, $filename);
            
        }
    }
}