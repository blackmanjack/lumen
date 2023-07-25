<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Node;
use App\Models\User;
use App\Models\Hardware;
// use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        try {
            $this->validate($request, [
                'name' => 'required',
                'location' => 'required',
                'id_hardware_node' => 'required|integer',
            ]);

            $node = new Node();
            $node->id_user = Auth::id();
            $node->name = $request->name;
            $node->location = $request->location;
            $node->id_hardware_node = $request->id_hardware_node;
            $node->id_hardware_sensor = $request->id_hardware_sensor;
            $node->field_sensor = $request->field_sensor;

            // VALIDATE id_hardware_node type should be for node
            $hardwareType = DB::table('hardware')
                ->select('type')
                ->where('id_hardware', $node['id_hardware_node'])
                ->value('type');

            if (empty($hardwareType)) {
                return response()->json([
                    'description' => 'Bad Request',
                    'status' => 400,
                    'message' => 'Id hardware for node not found',
                ], 400);
            }

            if ($hardwareType != 'single-board computer' && $hardwareType != 'microcontroller unit') {
                return response()->json([
                    'description' => 'Bad Request',
                    'status' => 400,
                    'message' => 'Hardware node type not match, type should be single-board computer or microcontroller unit',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'description' => 'Bad Request',
                'status' => 400,
                'message' => 'Missing parameter',
            ], 400);
        }

        try {
            $isPublic = isset($node['is_public']) ? (bool) $node['is_public'] : false;
        } catch (\Exception $e) {
            $isPublic = false;
        }

        // VALIDATE id_hardware_sensor exist and should be type sensor
        if (isset($node['id_hardware_sensor'])) {
            $arraysensor = $node['id_hardware_sensor'];
            foreach ($arraysensor as $x) {
                if ($x !== 'NULL') {
                    $rows = DB::select("SELECT hardware.type FROM hardware WHERE id_hardware = $x");
                    if (count($rows) > 0) {
                        $res_hardware = $rows[0];
                        if ($res_hardware->type == 'sensor') {
                            $valid = 1;
                        } else {
                            return response()->json([
                                'description' => 'Bad Request',
                                'status' => 400,
                                'message' => "Hardware sensor type not match, type should be sensor. id = $x"
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'description' => 'Bad Request',
                            'status' => 400,
                            'message' => "Id hardware for sensor not found. id = $x"
                        ], 400);
                    }
                }
            }
        } else {
            $arraysensor = '{NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL}';
        }

        // VALIDATE the field should be registered on existed sensor column
        //$arrayField = isset($node['field_sensor']) ? $node['field_sensor'] : [];

        if (isset($node['field_sensor'])) {
            $arrayfield = $node['field_sensor'];
            for ($x = 0; $x < count($arrayfield); $x++) {
                // dd($arrayfield[0], $arraysensor[0]);
                if ($arrayfield[$x] !== 'NULL') {
                    if ($arraysensor[$x] !== 'NULL') {
                        $valid = 1;
                    } else {
                        return response()->json([
                            'description' => 'Bad Request',
                            'status' => 400,
                            'message' => "Field sensor is empty. field = $x"
                        ], 400);
                    }
                }
            }
        } else {
            $arrayfield = '{NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL}';
        }

        $id_user = Auth::id();

        if(gettype($arraysensor) == 'string'){
            $id_hardware_sensor = $arraysensor;
        }else {
            $id_hardware_sensor = '{'.implode(',', $arraysensor).'}';
        }

        if(gettype($arrayfield) == 'string'){
            $field_sensor = $arrayfield;
        }else {
            $field_sensor = '{'.implode(',', $arrayfield).'}';
        }

        DB::table('node')->insert([
            'name' => $node['name'],
            'location' => $node['location'],
            'id_hardware_node' => $node['id_hardware_node'],
            'id_user' => $id_user,
            'is_public' => $isPublic,
            'id_hardware_sensor' =>  $id_hardware_sensor,
            'field_sensor' => $field_sensor,
        ]);

        return response()->json([
            'description' => 'Created',
            'status' => 201,
            'message' => 'Successfully add new node',
        ], 201);
}

    public function showAll()
    {
        $userid = Auth::id();
        $node = Node::where('id_user', $userid)->get();

        $node->transform(function ($item) {
            $item->feed->transform(function ($feedItem) {
                $feedItem->value = array_map(function ($value) {
                    if (is_numeric($value)) {
                        return strpos($value, '.') !== false ? floatval($value) : intval($value);
                    }
                    return $value;
                }, explode(',', trim($feedItem->value, '{}')));
    
                return $feedItem;
            });
    
            return $item;
        });

        return response($node);
    }


    public function showDetailData($id)
    {
        //query user and hardware
        $userid = Auth::id();

        // $node = Node::where('id_user', $userid)
        // ->where('id_node', $id)
        // ->with('feed')
        // // ->with('User','Hardware') To do: ->with('Feed')
        // ->first();

        $node = Node::where('id_user', $userid)
        ->where('id_node', $id)->get();

        $node->transform(function ($item) {
            $item->feed->transform(function ($feedItem) {
                $feedItem->value = array_map(function ($value) {
                    if (is_numeric($value)) {
                        return strpos($value, '.') !== false ? floatval($value) : intval($value);
                    }
                    return $value;
                }, explode(',', trim($feedItem->value, '{}')));
    
                return $feedItem;
            });
    
            return $item;
        });

        $findNode = Node::where('id_node', $id)->first();
        if($findNode){
            if($node){
                return response()->json($node, 200);
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
        //only accept headers application/x-www-form-urlencoded & application/json
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded" && $split !== "application/json"){
            $message = "Content-Type ".$split." Not Support, only accept application/x-www-form-urlencoded & application/json";
            return response()->json($message, 415);
        }
        
        $userid = Auth::id();

        $findNode = Node::where('id_node', $id)->first();

        $CheckuserID = Node::where('id_node', $id)->pluck('id_user')->first();

        $nodes = Node::where('id_user', $userid)->where('id_node', $id)->first(); 

        if($findNode){
            //To do add admin check, admin also can update user's node
            if($nodes && $CheckuserID == $userid){ 
                
                try {
                    $this->validate($request, [
                        'name' => 'required',
                        'location' => 'required',
                        'id_hardware_node' => 'required|integer',
                    ]);
                    
                    $node = new Node();
                    $node->id_user = Auth::id();
                    $node->name = $request->name;
                    $node->location = $request->location;
                    $node->id_hardware_node = $request->id_hardware_node;
                    $node->id_hardware_sensor = $request->id_hardware_sensor;
                    $node->field_sensor = $request->field_sensor;
                    $node->is_public = $request->is_public;
                    // VALIDATE id_hardware_node type should be for node
                    $hardwareType = DB::table('hardware')
                        ->select('type')
                        ->where('id_hardware', $node['id_hardware_node'])
                        ->value('type');
        
                    if (empty($hardwareType)) {
                        return response()->json([
                            'description' => 'Bad Request',
                            'status' => 400,
                            'message' => 'Id hardware for node not found',
                        ], 400);
                    }
        
                    if ($hardwareType != 'single-board computer' && $hardwareType != 'microcontroller unit') {
                        return response()->json([
                            'description' => 'Bad Request',
                            'status' => 400,
                            'message' => 'Hardware node type not match, type should be single-board computer or microcontroller unit',
                        ], 400);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'description' => 'Bad Request',
                        'status' => 400,
                        'message' => 'Missing parameter',
                    ], 400);
                }
        
                try {
                    $isPublic = isset($node['is_public']) ? (bool) $node['is_public'] : false;
                } catch (\Exception $e) {
                    $isPublic = false;
                }
        
                // VALIDATE id_hardware_sensor exist and should be type sensor
                if (isset($node['id_hardware_sensor'])) {
                    $arraysensor = $node['id_hardware_sensor'];
                    foreach ($arraysensor as $x) {
                        if ($x !== 'NULL') {
                            $rows = DB::select("SELECT hardware.type FROM hardware WHERE id_hardware = $x");
                            if (count($rows) > 0) {
                                $res_hardware = $rows[0];
                                if ($res_hardware->type == 'sensor') {
                                    $valid = 1;
                                } else {
                                    return response()->json([
                                        'description' => 'Bad Request',
                                        'status' => 400,
                                        'message' => "Hardware sensor type not match, type should be sensor. id = $x"
                                    ], 400);
                                }
                            } else {
                                return response()->json([
                                    'description' => 'Bad Request',
                                    'status' => 400,
                                    'message' => "Id hardware for sensor not found. id = $x"
                                ], 400);
                            }
                        }
                    }
                } else {
                    $arraysensor = '{NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL}';
                }
        
                // VALIDATE the field should be registered on existed sensor column
                $arrayField = isset($node['field_sensor']) ? $node['field_sensor'] : [];
        
                if (isset($node['field_sensor'])) {
                    $arrayfield = $node['field_sensor'];
                    for ($x = 0; $x < count($arrayfield); $x++) {
                        // dd($arrayfield[0], $arraysensor[0]);
                        if ($arrayfield[$x] !== 'NULL') {
                            if ($arraysensor[$x] !== 'NULL') {
                                $valid = 1;
                            } else {
                                return response()->json([
                                    'description' => 'Bad Request',
                                    'status' => 400,
                                    'message' => "Field sensor is empty. field = $x"
                                ], 400);
                            }
                        }
                    }
                } else {
                    $arrayfield = '{NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL}';
                }

                $checkNode = Node::find($id);
                if(gettype($arraysensor) == 'string'){
                    $id_hardware_sensor = $arraysensor;
                }else {
                    $id_hardware_sensor = '{'.implode(',', $arraysensor).'}';
                }
        
                if(gettype($arrayfield) == 'string'){
                    $field_sensor = $arrayfield;
                }else {
                    $field_sensor = '{'.implode(',', $arrayfield).'}';
                }

                $update = $checkNode->update([
                    'name' => $node['name'],
                    'location' => $node['location'],
                    'id_hardware_node' => $node['id_hardware_node'],
                    'id_user' => $userid,
                    'is_public' => $isPublic,
                    'id_hardware_sensor' => $id_hardware_sensor,
                    'field_sensor' => $field_sensor,
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
