<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Storage;

class ConverterController extends Controller
{
    
    public function convertFile($file)
    {
        // Prepare Output File
        $fileName = explode("/", $file);
        $fileName = $fileName[sizeof($fileName)-1];
        $fileLen = strlen($fileName);
        $ext = substr($fileName,$fileLen-4, 4);
        $fileName = substr($fileName,0,$fileLen-4);

        //make sure filename does not exits
        
        $cnt = '';
        do {
            $fileNewName = $fileName.$cnt;
            $fileCheck = "archive/".$fileNewName.".csv";

            $exists = Storage::disk('local')->exists($fileCheck);
            
            if ($exists) $cnt++;
        } while ($exists);
        
        $fileName = $fileName.$cnt;
        $fileMove = "archive/".$fileName.".csv";
        $fileOut = "converter/".$fileName."-processed.csv";

        $line="Payee-Name-1;Payee-Name-2;Payee-Street;Payee-City;Payee-Country;Payee-IBAN;Payee-BIC;Payee-Bank-Name-1;Payee-Bank-Country;Amount;Currency;Purpose";
        Storage::put($fileOut, $line);

        //Initialize variables
        $cnt = 0;
        $added = 0;
        $cols = array(
            array("BeneficiaryName",0,0),
            array("BeneficiaryAddressLine1",1,0),
            array("BeneficiaryAddressCity/Town",2,0),
            array("BeneficiaryAddressCountry",3,0),
            array("BeneficiaryAccountIBAN",4,0),
            array("BeneficiaryAccountBIC",5,0),
            array("PaymentAmount",6,0),
            array("ReserveField5",7,0),
            array("DescriptionofPurpose",8,0)
            );

        $newContents = "";

        $row = 0;
        $contents = Storage::get($file);
        $contents = explode(PHP_EOL, $contents);

        foreach ($contents as $content){
            $data = str_getcsv($content,",",'');
            $line = ConverterController::recSave($data, $row, $cols);
            //print("<pre>".print_r($line,true)."</pre>");
            if (strlen($line) > 10){
                Storage::append($fileOut, $line);
                $added++;
            }
            $row++;
        }

        if ($added) {
            $msg = $fileOut;
            Log::debug('convertOne.file (Converted) = '.$file);
        } else {
            $msg = false;
            Storage::delete($fileOut);
            Log::debug('convertOne.file (NOT Converted) = '.$file);
        }

        // move uploaded file to file archive
        Storage::move($file, $fileMove);

        return $msg;  // return converted filename
    }

    public function recSave ($data, $row, &$cols)
    {
        $line='';
        $csvSep = ";";
        $num = count($data);
        $numcols = count($cols);
        if ($row==0){

            for ($c1=0; $c1<$numcols; $c1++){
                $col = -1;
                foreach($data as $dta){
                    $col++;
                    $dta = trim($dta);
                    if ($cols[$c1][0]== $dta){
                        $cols[$c1][2]=$col;
                        break;
                    }
                }
            }

        } elseif ($num>8) {
            $line = $data[$cols[0][2]].$csvSep;                     //Col 0 - Payee-Name-1
            $line .=$csvSep;                                        //Col 1 - Payee-Name-2
            $line .= $data[$cols[1][2]].$csvSep;                    //Col 2 - Payee-Street
            $line .= $data[$cols[2][2]].$csvSep;                    //Col 3 - Payee-City
            $line .= $data[$cols[3][2]].$csvSep;                    //Col 4 - Payee-Country
            $line .= $data[$cols[4][2]].$csvSep;                    //Col 5 - Payee-IBAN
            $line .= $data[$cols[5][2]].$csvSep;                    //Col 6 - Payee-BIC
            $line .= $csvSep;                                       //Col 7 - Payee-Bank-Name-1
            $line .= substr($data[$cols[4][2]],0,2).$csvSep;          //Col 8 - Payee-Bank-Country
            $line .= $data[$cols[6][2]].$csvSep;                    //Col 9 - Amount
            $line .= $data[$cols[7][2]].$csvSep;                    //Col 10- Currency
            //print("<pre>".print_r($line,true)."</pre>");
            $line .= $data[$cols[8][2]];                            //Col 11- Purpose
        }
        return $line;
    }

    /******************************************************************
     * Convert uploaded files to required CSV format
     * 
     * 1. List directory and clean each file
     * 2. Convert each file and save it in a folder
     * 3. Send email notification
     ******************************************************************/
    public function convert()
    {
        // Get list all files in public folder
        $files = Storage::files('public');

        // Filter files that have .csv extension
        //$files = preg_grep('^.*\.(?!csv).*$', $allFiles);
        foreach($files as $file)
        {
            $extension = explode('.', $file);
            $extension = end($extension);
            if ($extension == 'csv'){
                if (ConverterController::convertFile($file)){
                    print("File converted is: ".$file);
                }
            }

        }
    }

    public function convertOne($file)
    {
        $result = null;
        Log::debug('convertOne.file = '.$file);
        $extension = explode('.', $file);
        $extension = end($extension);
        if ($extension == 'csv'){
            $result = ConverterController::convertFile($file);
        }
        return $result;
    }

}
