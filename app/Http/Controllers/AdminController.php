<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function showAllDataUser()
    {
        $id = Auth::id();
        $isAdmin = User::where('id', $id)->pluck('is_admin')->first();
        if($isAdmin){
            $data = User::with('Node.Hardware', 'Node.Sensor.Channel')->get();
            return response()->json($data, 200);
        }
        else if(!$isAdmin){
            $message = 'You are not administrator';
            return response()->json($message, 403);
        }
        else{
            $message = "User Not found";
            return response()->json($message, 404);
        }
    }
}


