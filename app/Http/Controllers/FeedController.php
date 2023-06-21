<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Feed;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedController extends Controller
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
            'id_node' => 'required',
        ]);
        
        $findNode = Node::where('id_node', $request->id_node)->first();

        if($findNode){
            $data = $request->all();

            $arrayvalue = $data["value"];
            
            $dt_value = explode(',', trim($arrayvalue, '{}'));

            $res = $findNode;
            foreach ($dt_value as $x => $value) {
                if ($value != 'NULL') {
                    if ($res["field_sensor"][$x] && $res["field_sensor"][$x] != 'NULL') {
                        $valid = 1;
                    } else {
                        return response()->json([
                            'description' => 'Bad Request',
                            'status' => 400,
                            'message' => 'Field sensor is empty. field = ' . $x,
                        ], 400);
                    }
                }
            }

            DB::table('feed')->insert([
                'value' => $request->value,
                'id_node' => $request->id_node,
                'time' => Carbon::now()
            ]);
            $message = "Success add new Channel";
            return response()->json($message, 201);
        }else{
            $message = "Node Not Found";
            return response()->json($message, 404);
        }
    }
}
