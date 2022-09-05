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
    }

    public function showAll()
    {
        $userid = Auth::id();
        $data = Node::where('user_id', $userid)->get();
        return response($data);
    }

    public function showDetailData($id)
    {
        //query user and hardware
        $userid = Auth::id();

        $data = Node::where('user_id', $userid)
        ->where('id', $id)
        ->with('User','Hardware', 'Sensor.Channel')
        ->first();

        $findNode = Node::find($id);
        if($findNode){
            if($data){
                return response()->json($data, 200);
            }else{
                $message = "You can\'t see another user\'s node";
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id node not found';
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

        $findNode = Node::find($id);
        $data = Node::where('user_id', $userid)->find($id); 
        if($findNode){
            if($data){
                $update = $data->update([
                    'name'=> $request->name,
                    'location'=> $request->location,
                ]);
        
                $message = "Success edit node";
                return response()->json($message, 200);
            }else{
                $message = "You can\'t edit another user\'s node";
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id node not found';
            return response()->json($message, 403);
        }
    }

    public function delete($id)
    {
        $userid = Auth::id();
        $data = Node::where('user_id', $userid)->find($id);

        $findNode = Node::find($id);
        if($findNode){
            if($data){
                $data->delete();
                $message = "Success delete node, id: $id";
                return response()->json($message, 200);
            }else{
                $message = 'You can\'t edit another user\'s node';
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id node not found';
            return response()->json($message, 404);
        }
    }
}
