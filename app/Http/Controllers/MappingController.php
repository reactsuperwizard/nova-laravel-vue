<?php

namespace App\Http\Controllers;

use App\Mapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MappingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Mapping  $mapping
     * @return \Illuminate\Http\Response
     */
    public function show(Mapping $mapping)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mapping  $mapping
     * @return \Illuminate\Http\Response
     */
    public function edit(Mapping $mapping)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mapping  $mapping
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mapping $mapping)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mapping  $mapping
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mapping $mapping)
    {
        //
    }

    /*************************************************
     *  Map Columns - maps the headers in the csv file to columns
     *      in the transactions table
     * 
     *  @param  $headers - a two dimensional array containing the column
     *          name in one dimension and expecting the
     *          field name in the other
     * 
     *  @returns $headers
     */
    public function mapTransactionHeaders($headers){
    }

    /****************************************************
     *  Populate Mapping Table
     */
    public function populate(){
        $map = new Mapping;

        $rows = array (
            "mandator"=>"providerID",
            "provider_id"=>"entryID",
            "customer_id"=>"customerID",
            "star1"=>"star1",
            "star2"=>"star2",
            "num_combi"=>"prediction",
            "drawdate"=>"eventID",
            "stake"=>"stake",
            "gametype"=>"eventType",
            "proverentryid"=>"entryID",
            "ownerid"=>"customerID",
            "startdate"=>"eventID",
            "ticketid"=>"ticketID",
            "entry649combi"=>"prediction",
            "row_no"=>"entryType"
        );

        foreach($rows as $x => $x_value) {
            echo "Key=" . $x . ", Value=" . $x_value;
            echo "<br>";

            Mapping::create(["csvHeading"=>$x,"field"=>$x_value]);
            // $map->csvHeading = $x;
            // $map->field = $x_value;
            // $map->save();
        }
        return response("Done", 200);
    }
}
