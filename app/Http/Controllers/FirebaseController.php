<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends Controller
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

    public function index(){
        return view('notification');
    }

    public function store(){
        $title = request('title');
        $message = request('message');
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/toppers-pakistan-firebase-adminsdk-aeb8a-492d961c12.json');
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://toppers-pakistan.firebaseio.com/')
        ->create();

        $database = $firebase->getDatabase();

        $newPost = $database
        ->getReference('notifications')
        ->push([
        'title' => $title ,
        'message' => $message
        ]);
        echo '<pre>';
        print_r($newPost->getvalue());
        return redirect('notification');    
    }
}
