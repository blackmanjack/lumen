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
                'id_node' => 'required|integer',
                'name' => 'required|string|max:256',
                'unit' => 'required|string|max:256'
            ]);
    
            $data = new Sensor();
            $data->id_node = $request->id_node;
            $data->name = $request->name;
            $data->unit = $request->unit;
    
            $id_user = Node::where('id_node', $request->id_node)->pluck('id_user')->first();
            //checking id node
            $findNode = Node::where('id_node', $request->id_node)->first();
            if($findNode){
                //check user's node
                if($id_user !== Auth::id()){
                    $message = 'You can\'t use another user\'s node';
                    return response()->json($message, 403);
                }
                //checking hardware
                if($request->id_hardware == null || $request->id_hardware == ""){
    
                    $save = $data->save();
                    $message = "Success add new sensor";
                    return response()->json($message, 201);
                }else{
                    $data->id_hardware = $request->id_hardware; 
                    //check hardware id
                    $hardwareId = Hardware::where('id_hardware', $request->id_hardware)->first();
                    if($hardwareId){
                        //checking type hardware
                        $typeHardware = Hardware::where('id_hardware', $request->id_hardware)->pluck('type')->first();
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
            $data = Sensor::whereHas('node', function ($query) {
                    $query->where('id_user', Auth::id());
                    })   
                    ->get();
            return response($data);
    }

    public function showDetailData($id)
    {
            //query node, hardware, channel 
            $findSensor = Sensor::where('id_sensor', $id)->first();
            if($findSensor){
                $data = Sensor::with('channel')
                ->whereHas('node', function ($query) {
                    $query->where('id_user', Auth::id());
                })
                ->find($id);

                //user validation
                if (!$data) {
                    $message = "You can't see another user's sensor";
                    return response()->json($message, 403);
                }
                
            return response()->json($data, 200);

            }else {
                $message = 'Id sensor not found';
                return response()->json($message, 404);
            }
    }

    public function update(Request $request, $id)
    {
            //only accept headers application/x-www-form-urlencoded & application/json"
            $contentType = $request->headers->get('Content-Type');
            $split = explode(';', $contentType)[0];
            if($split !== "application/x-www-form-urlencoded" && $split !== "application/json"){
                $message = "Content-Type ".$split." Not Support, only accept application/x-www-form-urlencoded & application/json";
                return response()->json($message, 415);
            }
            
            //validation input
            $this->validate($request, [
                'name' => 'required|string|max:256',
                'unit' => 'required|string|max:256'
            ]);

            $findSensor = Sensor::find($id);
            $data = Sensor::where('id_sensor', $id)->with('Node')->first();
            if($findSensor){
                $userID = $data->toArray()['node']['id_user'];
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

    public function delete($id)
    {
            $findSensor = Sensor::find($id);
            $data = Sensor::where('id_sensor', $id)->with('Node')->first();
            if($findSensor){
                $userID = $data->toArray()['node']['id_user'];
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
