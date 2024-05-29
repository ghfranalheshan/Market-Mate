<?php

namespace App\Http\Controllers;

use App\Models\WithDelivery;
use App\Http\Requests\StoreWithDeliveryRequest;
use App\Http\Requests\UpdateWithDeliveryRequest;

class WithDeliveryController extends Controller
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
     * @param  \App\Http\Requests\StoreWithDeliveryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWithDeliveryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WithDelivery  $withDelivery
     * @return \Illuminate\Http\Response
     */
    public function show(WithDelivery $withDelivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWithDeliveryRequest  $request
     * @param  \App\Models\WithDelivery  $withDelivery
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWithDeliveryRequest $request, WithDelivery $withDelivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WithDelivery  $withDelivery
     * @return \Illuminate\Http\Response
     */
    public function destroy(WithDelivery $withDelivery)
    {
        //
    }
}
