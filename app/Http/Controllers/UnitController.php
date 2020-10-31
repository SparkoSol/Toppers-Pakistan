<?php

namespace App\Http\Controllers;

use App\Unit;
use App\Item;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function index()
    {
        $units = Unit::all();
        return view('unit',compact('units'));
    }

    public function addUnit()
    {
        return view('unit.add-unit');
    }

    public function storeUnit() {
        $unit = new Unit();
        $unit->name = request('name');

        $unit->save();
        return $unit;
    }

    public function editUnit($id)
    {
        $unit = unit::where('id', $id)->first();
        return view('unit.edit-unit',compact('unit'));
    }

    public function updateUnit($id)
    {
        $name = request('name');
        return unit::where('id', $id)
                ->update(['name'  => $name]);
    }

    public function deleteUnit($id)
    {
        try {
            unit::where('id', $id)
                ->delete();
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Please delete all Products relevant to this unit inorder to remove this unit.',
            ],200);
        }
    }

    public function apiIndex()
    {
        return Unit::all();
    }

    public function apiIndexById($id)
    {
        return [Unit::where('id', $id)->first()];
    }

    public function getById($id) {
        return Unit::where('id', $id)->first();
    }

    public function getProducts($id) {
        return Item::where('unit_id', $id)->get();
    }
}
