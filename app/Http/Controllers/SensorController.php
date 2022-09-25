<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Node;
use App\Models\Sensor;
use App\Models\Hardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
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
            'node_id' => 'required',
            'name' => 'required',
            'unit' => 'required'
        ]);

        $data = new Sensor();
        $data->node_id = $request->node_id;
        $data->name = $request->name;
        $data->unit = $request->unit;

        $user_id = Node::where('id', $request->node_id)->pluck('user_id')->first();
        //checking id node
        $findNode = Node::where('id', $request->node_id)->first();
        if($findNode){
            //check user's node
            if($user_id !== Auth::id()){
                $message = 'You can\'t use another user\'s node';
                return response()->json($message, 403);
            }
            //checking hardware
            if($request->hardware_id == null || $request->hardware_id == ""){

                $save = $data->save();
                $message = "Success add new sensor";
                return response()->json($message, 201);
            }else{
                $data->hardware_id = $request->hardware_id; 
                //check hardware id
                $hardwareId = Hardware::where('id', $request->hardware_id)->first();
                if($hardwareId){
                    //checking type hardware
                    $typeHardware = Hardware::where('id', $request->hardware_id)->pluck('type')->first();
                    if($typeHardware == 'sensor'){
                        $save = $data->save();
                        $message = "Success add new sensor";
                        return response()->json($message, 201);
                    }else{
                        $message = 'Hardware type not match, type should Sensor';
                        return response()->json($message, 400);
                    }
                }else {
                    $message = 'Hardware Not Found';
                    return response()->json($message, 404);
                }
            }
        }else{
            $message = 'Id node not found';
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Sensor::select('sensors.*')->leftJoin('nodes', 'nodes.id', '=', 'sensors.node_id')
                ->where('nodes.user_id', Auth::id())
                ->with('Node')    
                ->get();
        return response($data);
    }

    public function showDetailData($id)
    {
        //query node, hardware, channel 
        $data = Sensor::where('id', $id)->with('Node', 'Channel')->first();
        //add You can\'t see another user\'s sensor
        if($data){
            $userID = $data->toArray()['node']['user_id'];
            if($userID === Auth::id()){
                return response()->json($data, 200);
            }else{
                $message = 'You can\'t see another user\'s sensor';
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id sensor not found';
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

        //validation input
        $this->validate($request, [
            'name' => 'required',
            'unit' => 'required'
        ]);

        $findSensor = Sensor::find($id);

        $data = Sensor::where('id', $id)->with('Node', 'Channel')->first();

        $userID = $data->toArray()['node']['user_id'];
        if($findSensor){
            if($userID === Auth::id()){
                $update = $data->update([
                    'name'=> $request->name,
                    'unit'=> $request->unit,
                ]);

                $message = "Success edit Sensor";
                return response()->json($message, 200);
            }else{
                $message = 'You can\'t edit another user\'s sensor';
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id sensor not found';
            return response()->json($message, 404);
        }
    }

    public function delete($id)
    {
        $findSensor = Sensor::find($id);
        $data = Sensor::where('id', $id)->with('Node')->first();
        if($findSensor){
            $userID = $data->toArray()['node']['user_id'];
            if($userID === Auth::id()){
                $delete = $data->delete();

                $message = "Success delete sensor data, id: $id";
                return response()->json($message, 200);
            }else{
                $message = 'You can\'t delete another user\'s sensor';
                return response()->json($message, 403);
            }
        }else{
            $message = 'Id sensor not found';
            return response()->json($message, 404);
        }
    }
}
