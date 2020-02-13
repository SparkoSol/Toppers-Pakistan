<?php

namespace App\Http\Controllers;

use App\Carosel;
use Illuminate\Http\Request;

class CaroselController extends Controller
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
        $carosels = Carosel::all();
        return view('carosel',compact('carosels'));
    }

    public function addcarosel()
    {
        return view('carosel.add-carosel');
    }

    public function storeCarosel() {
        $carosel = new Carosel();
        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/carosel'), $imageName);
        $carosel->image = $imageName;
        $carosel->save();
        return redirect('/carosel');
    }

    public function editCarosel($id)
    {
        $carosel = Carosel::where('id', $id)->first();
        return view('carosel.edit-carosel',compact('carosel'));
    }

    public function updateCarosel($id)
    {

        if(request('image') != null){
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/carosel'), $imageName);
            Carosel::where('id', $id)
                ->update(['image' => $imageName]);
        }
        
        return redirect('/carosel');
    }

    public function deleteCarosel($id)
    {
        Carosel::where('id', $id)
                ->delete();
        
        return redirect('/carosel');
    }


    public function indexApi(){
        return Carosel::all();
    }

}

