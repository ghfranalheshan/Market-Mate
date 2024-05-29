<?php

namespace App\Http\Controllers;

use App\Models\MarketAccept;
use App\Http\Requests\StoreMarketAcceptRequest;
use App\Http\Requests\UpdateMarketAcceptRequest;

class MarketAcceptController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarketAcceptRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarketAcceptRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MarketAccept  $marketAccept
     * @return \Illuminate\Http\Response
     */
    public function show(MarketAccept $marketAccept)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarketAcceptRequest  $request
     * @param  \App\Models\MarketAccept  $marketAccept
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarketAcceptRequest $request, MarketAccept $marketAccept)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MarketAccept  $marketAccept
     * @return \Illuminate\Http\Response
     */
    public function destroy(MarketAccept $marketAccept)
    {
        //
    }
}
