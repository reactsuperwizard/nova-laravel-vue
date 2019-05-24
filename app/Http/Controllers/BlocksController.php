<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Blocks;
use App\Transactions;

use Illuminate\Http\Request;

class BlocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('test');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $blockNo = $request->input('blockNo');
        // print("<pre>".print_r($request,true)."</pre>");
        echo "Block: ".$blockNo."<br>";
        return $this->verifyBlock($blockNo);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $blockNo = $request->input('listNo');
        // print("<pre>".print_r($request,true)."</pre>");
        echo "Block: ".$blockNo."<br>";
        return $this->listBlock($blockNo);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blocks  $blocks
     * @return \Illuminate\Http\Response
     */
    public function show(Blocks $blocks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blocks  $blocks
     * @return \Illuminate\Http\Response
     */
    public function edit(Blocks $blocks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blocks  $blocks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blocks $blocks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blocks  $blocks
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blocks $blocks)
    {
        //
    }

    /****************************************************
     *  Create Genesis Block
     */
    public function genesis(){
        DB::table('transactions')->truncate();
        $trans = new Transactions;
        $trans->blockID = 1;
        $trans->entryID = 1;
        $trans->entryType = 'one';
        $trans->providerID = 1;
        $trans->customerID = 1;
        $trans->customerHash = '';
        $trans->eventType = 0;
        $trans->eventID = 0;
        $trans->ticketID = '0';
        $trans->stake = 0.0;
        $trans->prediction = "1~2~3~4~5";
        $trans->star1 = 1;
        $trans->star2 = 2;
        $trans->save();

        DB::table('blocks')->truncate();
        $block = new Blocks;
        $block->blockDate = date("Ymd His");
        $block->signed = '99';
        $block->firstTransID = 1;
        $block->lastTransID = 1;
        $block->blockHash = $this->getHash(1,1,1);
        $block->parentHash = $this->getHash(1,1,1);
        $block->signature2 = '';
        $block->sig2MemberID = 1;                       //in record 1 this holds the last transID blocked
        $block->sig2Date = '';
        $block->signature3 = '';
        $block->sig3MemberID = 0;
        $block->sig3Date = '';
        $block->signature4 = '';
        $block->sig4MemberID = 0;
        $block->sig4Date = '';
    
        $block->save();

        $this->getSignatureBaaS(1);
        return response("Genesis Done", 200);
    }

    /************************************************
     * Generate a hash combining the selected rows
     * 
     */
    public function getHash($firstTrans, $lastTrans, $blockNo){
        $ctx = hash_init('sha256');
        for ($i=$firstTrans; $i<=$lastTrans; $i++){
            set_time_limit(10);
            $trans = Transactions::find($i);
            $trans->blockID = $blockNo;

            $buffer =  $trans->created_at;
            $buffer .= $trans->blockID;
            $buffer .= $trans->entryID;
            $buffer .= $trans->entryType;
            $buffer .= $trans->providerID;
            $buffer .= $trans->customerID;
            $buffer .= $trans->customerHash;
            $buffer .= $trans->eventType;
            $buffer .= $trans->eventID;
            $buffer .= $trans->ticketID;
            $buffer .= $trans->stake;
            $buffer .= $trans->prediction;
            $buffer .= $trans->star1;
            $buffer .= $trans->star2;

            $trans->save();
            hash_update($ctx, $buffer);
        }
        $hash = hash_final($ctx);
        return $hash;
    }

    /***********************************************
     * Generate Signature for the block
     * 
     */
    public function getSignatureBaaS($blockNo){
        $secret = "!qB?b+L;{q[.g<=uybYiq,ZW.>GqsoI5F4iDSium3CWzP]s}P}.";
        $ctx = hash_init('sha256');
        $block = Blocks::find($blockNo);
        $buffer = $secret;
        $buffer .= $blockNo;
        // $buffer .= $block->updated_at;
        $buffer .= $block->blockHash;
        $buffer .= $block->parentHash;
        hash_update($ctx, $buffer);
        $hash = hash_final($ctx);
        $block->blockDate = date("Ymd His");
        $block->signatureBaaS = $hash;
        $block->save();
        return $hash;
    }

    /************************************************
     * Verify Block Integrity
     */
    public function verifyBlock($blockNo=217){
        $block = Blocks::find($blockNo);
        $firstTrans = $block->firstTransID;
        $lastTrans = $block->lastTransID;
        $hash = $block->blockHash;

        $newHash = $this->getHash($firstTrans, $lastTrans, $blockNo);

        $flag = false;
        if ($hash === $newHash) $flag = true;

        if ($flag){
            echo "Block ".$blockNo." Integrity is: Confirmed<br>";
            echo "Block hash = ".$hash."<br>";
            echo "Calculated hash = ".$newHash."<br>";
        } else {
            echo "Block ".$blockNo." Integrity is: Not Confirmed<br>";
            echo "Block hash = ".$hash."<br>";
            echo "Calculated hash = ".$newHash."<br>";
        }
        return (string) $flag;
    }

    /************************************************
     * List Block Transactions
     */
    public function listBlock($blockNo=217){
        $block = Blocks::find($blockNo);
        $firstTrans = $block->firstTransID;
        $lastTrans = $block->lastTransID;
        $hash = $block->blockHash;

        echo "<table border=1 cellpadding=3>";

        echo "<tr>";
        echo "<th>BlockID</th>";
        echo "<th>Created_at</th>";
        echo "<th>EntryID</th>";
        echo "<th>EntryType</th>";
        echo "<th>ProviderID</th>";
        echo "<th>CustomerID</th>";
        echo "<th>CustomerHash</th>";
        echo "<th>EventType</th>";
        echo "<th>EventID</th>";
        echo "<th>TicketID</th>";
        echo "<th>Stake</th>";
        echo "<th>Prediction</th>";
        echo "<th>Star1</th>";
        echo "<th>Star2</th>";
        echo "</tr>";

        for ($i=$firstTrans; $i<=$lastTrans; $i++){
            set_time_limit(10);
            $trans = Transactions::find($i);
            
            echo "<tr>";
            echo "<td>".$trans->blockID."</td>";
            echo "<td>".$trans->created_at."</td>";
            echo "<td>".$trans->entryID."</td>";
            echo "<td>".$trans->entryType."</td>";
            echo "<td>".$trans->providerID."</td>";
            echo "<td>".$trans->customerID."</td>";
            echo "<td>".$trans->customerHash."</td>";
            echo "<td>".$trans->eventType."</td>";
            echo "<td>".$trans->eventID."</td>";
            echo "<td>".$trans->ticketID."</td>";
            echo "<td>".$trans->stake."</td>";
            echo "<td>".$trans->prediction."</td>";
            echo "<td>".$trans->star1."</td>";
            echo "<td>".$trans->star2."</td>";
            echo "</tr>";
        }
        echo "</table>";
        return;
    }

}
