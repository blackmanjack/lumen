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
        $idSensor = $request->id_sensor;
        $findSensor = Sensor::where('id_sensor', $idSensor)->first();

        if($findSensor){
            $rows = DB::table('node')
            ->leftJoin('sensor', 'sensor.id_node', '=', 'node.id_node')
            ->where('sensor.id_sensor', $idSensor)
            ->select('node.id_user')
            ->get();
    
            if (count($rows) > 0) {
                $iduser = $rows[0]->id_user;
                //check userid based on header
                $username = $request->getUser();
                $userid = DB::table('user_person')->where('username', $username)
                                        ->pluck('id_user')
                                        ->first();

                if ($userid == $iduser) {
                    DB::table('channel')->insert([
                        'value' => $request->value,
                        'id_sensor' => $request->id_sensor,
                        'time' => Carbon::now()
                    ]);
                    $message = "Success add new Channel";
                    return response()->json($message, 201);
                } else {
                    return response()->json(["description" => "Forbidden", "status" => 403, "message" => "You can't send channel to another user's sensor"], 403);
                }
            } else {
                return response()->json(["description" => "Bad Request", "status" => 400, "message" => "Id sensor not found"], 400);
            }
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
