<?php

namespace App\Http\Controllers;

use App\Models\BuyingOrder;
use App\Models\M_category;
use App\Http\Requests\StoreM_categoryRequest;
use App\Http\Requests\UpdateM_categoryRequest;
use App\Models\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $category = M_category::query()->get();
        return view('MCategory.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('MCategory.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreM_categoryRequest $request
     */
    public function store(StoreM_categoryRequest $request)
    {
        M_category::query()->create([
            'name' => $request->name,
        ]);

        return redirect()->route('MCategory.index')
            ->with('Success', ' Category created successfully');

    }

    /**
     * Display the specified resource.
     *
     */
    public function show($m_category)
    {
        $m_category = M_category::query()->where('id', '=', $m_category)->first();

        return view('MCategory.show', compact('m_category'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Application|Factory|View
     */

    public function edit($m_category)
    {
        $m_category = M_category::query()->where('id', '=', $m_category)->first();

        return view('MCategory.edit', compact('m_category'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateM_categoryRequest $request
     * @param \App\Models\M_category $m_category
     * @return RedirectResponse
     */
    public function update(UpdateM_categoryRequest $request, $m_category)
    {
        M_category::query()->where('id', '=', $m_category)->update(['name' => $request->name]);


        return redirect()->route('MCategory.index')
            ->with('Success', ' Category updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy($m_category)
    {
        DB::table('m_categories')->where('id', $m_category)->delete();

        return redirect()->route('MCategory.index')
            ->with('Success', 'Category deleted successfully');

    }

    public function getAll()
    {
        $this->rejectExpiredOrders();


        $category = M_category::query()->get();
        return response()->json($category, \Symfony\Component\HttpFoundation\Response::HTTP_OK);

    }

    public static function rejectExpiredOrders()
    {
        $n = Carbon::now();
        $sub = $n->addDays(-1);
       // $now = Carbon::now();
        BuyingOrder::query()
            ->where('created_at', '<', $sub)
            ->update([
                'request_status'=>'rejected'
            ]);
    }
}
