<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Channel;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'id_sensor' => 'required',
        ]);
        
        $findSensor = Sensor::where('id_sensor', $request->id_sensor)->first();

        if($findSensor){
            DB::table('channel')->insert([
                'value' => $request->value,
                'id_sensor' => $request->id_sensor,
                'time' => Carbon::now()
            ]);
            $message = "Success add new Channel";
            return response()->json($message, 201);
        }else{
            $message = "Sensor Not Found";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Channel::select('channel.*')
                ->leftJoin('sensor', 'channel.id_sensor', '=', 'sensor.id_sensor')
                ->leftJoin('node', 'node.id_node', '=', 'sensor.id_node')
                ->where('node.id_user', Auth::id())
                ->with('Sensor')    
                ->get();

        return response($data);
    }

    public function showDetailData($id)
    {
        $data = Channel::select('channel.*')
                ->leftJoin('sensor', 'channel.id_sensor', '=', 'sensor.id_sensor')
                ->leftJoin('node', 'node.id_node', '=', 'sensor.id_node')
                ->where('node.id_user', Auth::id())
                ->where('channel.id', $id)
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

}
