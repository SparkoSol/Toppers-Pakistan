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
        error_log('here');
        try {
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

        return $data;
        } catch( \Throwable $e) {
            error_log($e);
        }
    }

    public function get() {
        $db = new FirestoreClient([
            'projectId'=> 'toppers-pakistan'
        ]);
        $data = $db->collection('notifications');
        dd($db);
        $newData = $data->documents();
        $notifications = [];
        foreach ($data as $item) {
            $obj = new Notification();
            $obj->id = $item->id();
            $obj->title = $item['title'];
            $obj->message = $item['message'];
            array_push($notifications, $obj);
        }
        return $notifications;
    }
    public function delete($id) {
        $db = new FirestoreClient([
            'projectId'=> 'toppers-pakistan'
        ]);
        $db->collection('notifications')->document($id)->delete();
    }
}

class Notification {
    public $id;
    public $title;
    public $message;
}
