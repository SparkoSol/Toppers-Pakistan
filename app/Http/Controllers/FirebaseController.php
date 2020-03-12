<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Google\Cloud\Firestore\FirestoreClient;
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

        $db = new FirestoreClient([
            'projectId'=> 'toppers-pakistan'
        ]);

        $title = request('title');
        $message = request('message');
        $time = now();
        $data = [
            'title' => $title,
            'message' => $message,
            'timestamp' => $time 
        ];

        $addedDocRef = $db->collection('notifications')->newDocument();
        $addedDocRef->set($data);
        
        return redirect('notification');    
    }
}
