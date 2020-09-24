<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
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

    public function apiIndex()
    {
        return Supplier::all();
    }

    public function apiIndexById($id)
    {
        return Supplier::where('id', $id)->first();
	}

	public function store() {
        request()->validate([
            'name' => ['required', 'string'],
        ]);
    	$supplier = new Supplier();
    	$supplier->email =    request('email');
        $supplier->name =     request('name');
        $supplier->phone =    request('phone');
    	$supplier->save();
    	return $supplier;
	}
	public function update($id) {
        $supplier = Supplier::where('id',$id)->update([
            'name'  => request('name'),
            'email' => request('email'),
            'phone' => request('phone')
        ]);

        return $supplier;
	}
	public function delete($id) {
        try {
            error_log('here');
            Supplier::where('id', $id)
                    ->delete();
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Cannot delete Supplier.',
            ],200);
        }
    }
}
