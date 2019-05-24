<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Storage;
use File;
use App\Mapping;
use App\Transactions;
use App\Blocks;
use App\Status;
use App\WaitingProcess;

use Spatie\PdfToText\Pdf;
class ProcessController extends Controller
{

    public function pdfProcess() 
    {
        echo Pdf::getText('1.pdf', '');

            exit();
    }
    /*******************************************************
     * Processing of files into blocks
     * 
     * 1. Open csv file and read first line
     * 2. Build the conversion array
     * 3. Read all lines one by one abd save data in database
     * 4. Create blockchain
     * 
     */

     public function import($file_name)
     {
        // $filename = "D:\Develop\www\mbpapi\storage\pile.csv";
        $filename = "../storage/app/".$file_name;
        // $filename = "D:\pile.csv";
        // echo $filename,"<br>";
        // $filename = "/storage/app"$filename;

        $conv = array();
        $convSize = 0;
        $row = 1;
        if (($handle = fopen($filename, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                if ($num > 10) {
                    //echo "<p> $num fields in line $row: <br /></p>\n";
                    if ($row == 1){
                        $cnt=0;
                        for ($i=0;$i<$num;$i++){
                            // $field = Mapping::wherecsvHeader($data[$i]);
                            $map = Mapping::where('csvHeading', "=", $data[$i])->first();
                            if (isset($map->csvHeading)){
                                // echo "Field csv:".$map->csvHeading."<br>";
                                // echo "Sizeof:".sizeof($map)." - data[".$i."] = ".$data[$i]."<br>";
                                $conv[$map->field]=$i;
                                $cnt++;
                            }
                        }
                        if (!isset($conv['entryID'])) $conv['entryID']=0;
                        if (!isset($conv['entryType'])) $conv['entryType']=0;
                        if (!isset($conv['providerID'])) $conv['providerID']=0;
                        if (!isset($conv['customerID'])) $conv['customerID']=0;
                        if (!isset($conv['customerHash'])) $conv['customerHash']=0;
                        if (!isset($conv['eventType'])) $conv['eventType']=0;
                        if (!isset($conv['eventID'])) $conv['eventID']=0;
                        if (!isset($conv['ticketID'])) $conv['ticketID']=0;
                        if (!isset($conv['stake'])) $conv['stake']=0;
                        if (!isset($conv['prediction'])) $conv['prediction']=0;
                        if (!isset($conv['star1'])) $conv['star1']=0;
                        if (!isset($conv['star2'])) $conv['star2']=0;
                        print("<pre>".print_r($conv,true)."</pre>");
                        $convSize=$i;
                        $row++;
                        // Get next block to start a new block

                    } else {
                        set_time_limit(10);
                        // echo "entered else <br>";
                        $trans = new Transactions;
                        $trans->blockID = 0;
                        $trans->entryType = 'bet';
                        $trans->customerHash = '-';
                        $trans->ticketID = "0";
                        $trans->stake = "0";

                        $trans->entryID = $data[$conv['entryID']];
                        $trans->entryType = $data[$conv['entryType']];
                        $trans->providerID = $data[$conv['providerID']];
                        $trans->customerID = $data[$conv['customerID']];
                        $trans->customerHash = $data[$conv['customerHash']];
                        $trans->eventType = $data[$conv['eventType']];
                        $trans->eventID = $data[$conv['eventID']];
                        $trans->ticketID = $data[$conv['ticketID']];
                        $trans->stake = $data[$conv['stake']];
                        $trans->prediction = $data[$conv['prediction']];
                        $trans->star1 = $data[$conv['star1']];
                        $trans->star2 = $data[$conv['star2']];
                        $trans->save();
                        // print("<pre>".print_r($trans,true)."</pre>");
                        $row++;
                    }
                }
                if ($row % 100 == 0) {echo "Finished Transaction ",$i," <br>";} 
            }
            fclose($handle);
        }
        $this->blockTransactions();
        return;
    }
    /********************************************************
     * Start file processing
     * 
    */
    public function startProcessing()
    {
        
        $status = Status::find(1);

        // Check status of processing
        if ($status->status) {
            return;
        }

        // Pick one file
        $waiting_process = WaitingProcess::where('active', '1')->oldest()->first();
        Log::info($waiting_process);
        // Check whether waiting processing is existed or not
        if (empty($waiting_process)) {
            updateProcessingStatus(0);
            return;
        }
        $this->import($waiting_process->filename);
        return;

    }
    
    public function updateProcessingStatus($current_status)
    {
        $processing_status = Status::find(1);
        $processing_status->status = $current_status;
        $processing_status->save();

        return;
    }


    /*******************************************************************
     * Place transactions into blocks
     * 
     */
    public function blockTransactions (){
        $blk = new BlocksController();
        $maxBlockSize=100;
    
        // Get last Transaction ID
        $trans = DB::table('transactions')->orderBy('id', 'desc')->first();
        $lastTransID = $trans->id;

        // Start the block
        $flagMore = true;

        While ($flagMore){
            //Get previous Block blockHash and set it as parentHash
            $block = DB::table('blocks')->orderBy('id', 'desc')->first();
            $parentHash = $block->blockHash;
            $firstTransID = $block->lastTransID;

            $noOfTransactions = $lastTransID - $firstTransID;
            // Check if we have Transactions to Block
            if ($noOfTransactions < 1) { 
                return response ("Finished", 200);
            }

            if ($noOfTransactions <= $maxBlockSize){
                $currentLastTransID = $lastTransID;
                $flagMore=false;
            } elseif ($noOfTransactions > $maxBlockSize){
                $currentLastTransID = $firstTransID + $maxBlockSize;
            }

            $firstTransID++;
            $block = new Blocks;
            $block->firstTransID = $firstTransID;
            $block->lastTransID = $currentLastTransID;
            $block->parentHash = $parentHash;
            $block->blockDate = date("Ymd His");
            $block->signed = '0';
            $block->blockHash = '';
            $block->signature2 = '';
            $block->sig2MemberID = 0;
            $block->sig2Date = '';
            $block->signature3 = '';
            $block->sig3MemberID = 0;
            $block->sig3Date = '';
            $block->signature4 = '';
            $block->sig4MemberID = 0;
            $block->sig4Date = '';
            $block->save();

            $blockNo = $block->id;

            // Calculate this Block's last Transaction ID
            $hash = $blk->getHash($firstTransID,$currentLastTransID,$blockNo);
            $firstTransID=$currentLastTransID;
            $block->blockHash = $hash;
            $block->save();

            $blk->getSignatureBaaS($blockNo);
        }
        return response ("Finished - end", 200);
    }
}
