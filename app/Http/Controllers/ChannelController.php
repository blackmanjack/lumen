<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Channel;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChannelController extends Controller
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
            'value' => 'required',
            'sensor_id' => 'required',
        ]);

        $data = new Channel();
        $data->value = $request->value;
        $data->sensor_id = $request->sensor_id;
        
        $findSensor = Sensor::where('id', $request->sensor_id)->first();

        if($findSensor){
            $save = $data->save();
            $message = "Success add new Channel";
            return response()->json($message, 201);
        }else{
            $message = "Sensor Not Found";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Channel::select('channels.*')
                ->leftJoin('sensors', 'channels.sensor_id', '=', 'sensors.id')
                ->leftJoin('nodes', 'nodes.id', '=', 'sensors.node_id')
                ->where('nodes.user_id', Auth::id())
                ->with('Sensor')    
                ->get();

        return response($data);
    }

    public function showDetailData($id)
    {
        $data = Channel::select('channels.*')
                ->leftJoin('sensors', 'channels.sensor_id', '=', 'sensors.id')
                ->leftJoin('nodes', 'nodes.id', '=', 'sensors.node_id')
                ->where('nodes.user_id', Auth::id())
                ->where('channels.id', $id)
                ->with('Sensor')    
                ->first();

        if($data !== null){
            return response()->json($data, 200);
        }
        else{
            $message = "Channel Not found";
            return response()->json($message, 404);
            }
    }

    // public function update(Request $request, $id)
    // {
    //     $data = Channel::find($id);
    //     // dd($request->all());
    //     // return response($request);
    //     $update = $data->update([
    //         'name'=> $request->name,
    //         'unit'=> $request->unit,
    //     ]);

    //      if($update){
    //         $message = "Success edit Channel";
    //         return response()->json($message, 200);
    //     }else{
    //         $message = "Empty Request Body";
    //         return response()->json($message, 400);
    //     }
    //     return response($response);
    // }

    // public function delete($id)
    // {
    //     $data = Channel::find($id);
    //     $delete = $data->delete();

    //     if($delete){
    //         $message = "Success delete Channel, id: $id";
    //         return response()->json($message, 200);
    //     }else{
    //         $message = "Parameter is Invalid";
    //         return response()->json($message, 404);
    //     }
    //     // return response($response);
    // }
    // //
}
