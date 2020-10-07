<?php

namespace App\Http\Controllers;

use App\Carousel;
use Illuminate\Http\Request;

class CarouselController extends Controller
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
        return Carousel::all();
    }
    public function getById($id) {
        return Carousel::where('id', $id)->first();
    }

    public function store() {
        try {
            $carousel = new Carousel();
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/carousel'), $imageName);
            $carousel->image = $imageName;
            $carousel->save();
            return $carousel;
        } catch(\Throwable $e) {
            error_log($e);
        }
    }

    public function update($id)
    {
        try {
            $carousel = Carousel::where('id', $id)->first();
            if(request('image') != 'null'){
                if ($carousel->image != null) {
                    if (file_exists(public_path('images/carousel/').$carousel->image)) {
                        unlink(public_path('images/carousel/').$carousel->image);
                    }
                }
                $imageName = time().'.'.request()->image->getClientOriginalExtension();
                request()->image->move(public_path('images/carousel'), $imageName);
                Carousel::where('id', $id)
                    ->update(['image' => $imageName]);
                return $carousel;
            } else {
                return $carousel;
            }
        } catch (\Throwable $e) {
            error_log($e);
        }
    }

    public function delete($id)
    {
        $carousel = Carousel::where('id', $id)->first();
        if ($carousel->image != null) {
            if (file_exists(public_path('images/carousel/').$carousel->image)) {
                unlink(public_path('images/carousel/').$carousel->image);
            }
        }
        Carousel::where('id', $id)
                ->delete();
        return $carousel;
    }

    public function indexApi(){
        return Carousel::all();
    }
}

