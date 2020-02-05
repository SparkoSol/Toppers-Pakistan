<?php

namespace App\Http\Controllers;

use App\Unit;
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
        return redirect('/unit');
    }

    public function editUnit($id)
    {
        $unit = unit::where('id', $id)->first();
        return view('unit.edit-unit',compact('unit'));
    }

    public function updateUnit($id)
    {
        $name = request('name');
        unit::where('id', $id)
                ->update(['name'  => $name]);
        
        return redirect('/unit');
    }

    public function deleteUnit($id)
    {
        unit::where('id', $id)
                ->delete();
        
        return redirect('/unit');
    }
}
