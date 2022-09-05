<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Node;
use App\Models\Sensor;
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
            'hardware_id' => 'required',
            'name' => 'required',
            'unit' => 'required'
        ]);

        $data = new Sensor();
        $data->node_id = $request->node_id;
        $data->hardware_id = $request->hardware_id;
        $data->name = $request->name;
        $data->unit = $request->unit;
        $save = $data->save();

        if($save){
            $message = "Success add new sensor";
            return response()->json($message, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Sensor::all();
        return response($data);
    }

    public function showDetailData($id)
    {
        //query node, hardware, channel 
        
        $data = Sensor::where('id', $id)->with('Node', 'Channel')->first();
        //add You can\'t see another user\'s sensor
        $userID = $data->toArray()['node']['user_id'];
        if($data){
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
        
        $userID = $data->toArray()['node']['user_id'];
        if($findSensor){
            if($userID === Auth::id()){
                $delete = $data->delete();

                $message = "Succes delete sensor data, id: $id";
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
