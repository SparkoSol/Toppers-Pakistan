<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get() {
        return Notification::all();
    }
    public function getById($id) {
        return Notification::where('id', $id)->first();
    }
    public function store() {
        $notification = Notification::create([
            'title' => request('title'),
            'message' => request('message')
        ]);
        return $notification;
    }
    public function delete($id) {
        Notification::where('id',$id)->delete();
        return response()->json([
            'message' => 'Deleted Successfully'
        ],200);
    }
}
