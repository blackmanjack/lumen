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
            'name' => 'required',
            'location' => 'required'
        ]);

        $node = new Node();
        $node->id_user = Auth::id();
        $node->name = $request->name;
        $node->location = $request->location;
        
        if($request->id_hardware == null || $request->id_hardware == ""){
            $node = $node->save();
            $message = "Success add new node";
            return response()->json($message, 201);
        }

        $node->id_hardware = $request->id_hardware;
        $findHardware = Hardware::where('id_hardware', $request->id_hardware)->pluck('type')->first();
        if($findHardware){
            if($findHardware == 'microcontroller unit' || $findHardware == 'single-board computer'){
                $node = $node->save();
                $message = "Success add new node";
                return response()->json($message, 201);
            }else{
                $message = 'Hardware type not match, type should Microcontroller Unit or Single-Board Computer';
                return response()->json($message, 400);
            };
        }else{
            $message = 'Id hardware not found';
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $userid = Auth::id();
        $data = Node::where('id_user', $userid)->get();
        return response($data);
    }

    public function showDetailData($id)
    {
        //query user and hardware
        $userid = Auth::id();

        $data = Node::where('id_user', $userid)
        ->where('id_node', $id)
        ->with('User','Hardware')
        ->first();

        $findNode = Node::where('id_node', $id)->first();
        if($findNode){
            if($data){
                return response()->json($data, 200);
            }else{
                $message = 'You can\'t see another user\'s node';
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id node not found';
            return response()->json($message, 404);
        }
    }

    public function update(Request $request, $id)
    {
        //only accept headers application/x-www-form-urlencoded && application/json
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded" && $split !== "application/json"){
            $message = "Content-Type ".$split." Not Support, only accept application/x-www-form-urlencoded & application/json";
            return response()->json($message, 415);
        }
        
        $userid = Auth::id();

        $this->validate($request, [
            'name' => 'required',
            'location' => 'required',
        ]);

        $findNode = Node::where('id_node', $id)->first();

        $CheckuserID = Node::where('id_node', $id)->pluck('id_user')->first();

        $data = Node::where('id_user', $userid)->where('id_node', $id)->first(); 

        if($findNode){
            if($data && $CheckuserID == $userid){
                $update = $data->update([
                    'name'=> $request->name,
                    'location'=> $request->location,
                ]);
        
                $message = "Success edit node";
                return response()->json($message, 200);
            }else{
                $message = 'You can\'t edit another user\'s node';
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

        $findNode = Node::where('id_node', $id)->first();
        $CheckuserID = Node::where('id_node', $id)->pluck('id_user')->first();

        if($findNode){
            if($CheckuserID == $userid){
                $findNode->delete();
                $message = "Success delete node, id: $id";
                return response()->json($message, 200);
            }else{
                $message = 'You can\'t delete another user\'s node';
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id node not found';
            return response()->json($message, 404);
        }
    }
}
