<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Node;
use App\Models\User;
use App\Models\Hardware;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NodeController extends Controller
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

    public function create(Request $request)
    {   
        $this->validate($request, [
            'hardware_id' => 'required',
            'name' => 'required',
            'location' => 'required'
        ]);

        $node = new Node();
        $node->hardware_id = $request->hardware_id;
        $node->user_id = Auth::id();
        $node->name = $request->name;
        $node->location = $request->location;
        $node = $node->save();
        
        $message = "Success add new node";
        return response()->json($message, 201);

        // if($node){
        //     $message = "Success add new node";
        //     return response()->json($message, 201);
        // }else{
        //     $message = "Parameter is Invalid";
        //     return response()->json($message, 404);
        // }
    }

    public function showAll()
    {
        $userid = Auth::id();
        $data = Node::where('user_id', $userid)->get();
        // dd($data);
        $response=
            // 'data'=> $data
            $data
        ;
        return response($response);
    }

    public function showDetailData($id)
    {
        //query user and hardware
        $userid = Auth::id();

        $data = Node::where('user_id', $userid)
        ->where('id', $id)
        ->with('User','Hardware', 'Sensor.Channel')
        ->first();

        if($data){
            return response()->json($data, 200);
        }
        else{
            $message = "Not found";
            return response()->json($message, 404);
        }
    }

    public function update(Request $request, $id)
    {
        //only accept headers application/x-www-form-urlencoded
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded"){
            $message = "Supported format: application/x-www-form-urlencoded";
            return response()->json($message, 415);
        }
        
        $userid = Auth::id();

        $this->validate($request, [
            'name' => 'required',
            'location' => 'required'
        ]);

        $data = Node::where('user_id', $userid)->find($id);

        $update = $data->update([
            'name'=> $request->name,
            'location'=> $request->location,
        ]);

        $message = "Success edit node";
        return response()->json($message, 200);
        //add You can\'t edit another user\'s node


        // if($update){
        //     $message = "Success edit node";
        //     return response()->json($message, 200);
        // }else{
        //     $message = "Empty Request Body";
        //     return response()->json($message, 400);
        // }
        // return response($response);
    }

    public function delete($id)
    {
        $userid = Auth::id();
        $data = Node::where('user_id', $userid)->find($id);
        
        $delete = $data->delete();

        if($delete){
            $message = "Success delete node, id: $id";
            return response()->json($message, 200);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
        //add condition You can\'t delete another user\'s data and Id node not found
    }
}
