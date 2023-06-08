<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Node;
use App\Models\User;
use App\Models\Hardware;
use App\Models\Sensor;
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

        $id_user = Auth::id();

        DB::table('node')->insert([
            'name' => $node['name'],
            'location' => $node['location'],
            'id_hardware_node' => $node['id_hardware_node'],
            'id_user' => $id_user,
            'is_public' => $isPublic,
            'id_hardware_sensor' => '{'.implode(',', $arraysensor).'}',
            'field_sensor' => '{'.implode(',', $arrayfield).'}',
        ]);

        return response()->json([
            'description' => 'Created',
            'status' => 201,
            'message' => 'Successfully add new node',
        ], 201);
}


    public function areate(Request $request)
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
        $node = Node::where('id_user', $userid)->get();
        // $data = json_decode($node, true);
        // dd($node);
//         $data = json_decode($node, true);

// $fieldSensorString = $data[0]['field_sensor'];

// // Remove the surrounding curly braces {}
// $fieldSensorString = trim($fieldSensorString, '{}');

// // Split the string into an array using the comma as the delimiter
// $fieldSensorArray = str_getcsv($fieldSensorString, ',');

// // Replace the "NULL" values with actual null values in the array
// $fieldSensorArray = array_map(function ($value) {
//     return ($value === 'NULL') ? null : $value;
// }, $fieldSensorArray);
        // dd($node, $fieldSensorArray);
        return response($node);
    }

    public function showDetailData($id)
    {
        //query user and hardware
        $userid = Auth::id();

        $node = Node::where('id_user', $userid)
        ->where('id_node', $id)
        // ->with('User','Hardware') To do: ->with('Feed')
        ->first();

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
        //only accept headers application/x-www-form-urlencoded
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        // dd($split);
        if($split !== "application/x-www-form-urlencoded"){
            $message = "Supported format: application/x-www-form-urlencoded";
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
                    // dd($e);
                    // return response()->json([
                    //     'description' => 'Bad Request',
                    //     'status' => 400,
                    //     'message' => 'Missing parameter',
                    // ], 400);
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

                // dd($arraysensor, gettype($arraysensor));
        
                // dd($id);
                // $tes = DB::table('node')->where('id_node', $id)->first();
                // dd($tes);
                // DB::table('node')->where('id_node', $id)->update([
                //     'name' => $nodes['name'],
                //     'location' => $nodes['location'],
                //     'id_hardware_node' => $nodes['id_hardware_node'],
                //     'id_user' => $userid,
                //     'is_public' => $isPublic,
                //     'id_hardware_sensor' => '{'.implode(',', $arraysensor).'}',
                //     'field_sensor' => '{'.implode(',', $arrayfield).'}',
                // ]);
                // $response = Http::withHeaders([
                //     'Content-Type' => 'application/x-www-form-urlencoded',
                // ])->put('localhost:8000/node/3', [
                //     'name' => $nodes['name'],
                //     'location' => $nodes['location'],
                //     'id_hardware_node' => $nodes['id_hardware_node'],
                //     'id_user' => $userid,
                //     'is_public' => $isPublic,
                //     'id_hardware_sensor' => '{'.implode(',', $arraysensor).'}',
                //     'field_sensor' => '{'.implode(',', $arrayfield).'}',
                // ]);
                // $convArraySensor = '{'.implode(',', $arraysensor).'}';
                // $convArrayField = '{'.implode(',', $arrayfield).'}';
                $tes = Node::find($id);
                // // dd($tes, $arraysensor);
                $update = $tes->update([
                    'name' => $node['name'],
                    'location' => $node['location'],
                    'id_hardware_node' => $node['id_hardware_node'],
                    'id_user' => $userid,
                    'is_public' => $isPublic,
                    'id_hardware_sensor' => '{'.implode(',', $arraysensor).'}',
                    'field_sensor' => '{'.implode(',', $arrayfield).'}',
                ]);

                // dd($isPublic, gettype($isPublic));

    //             $sql = "
    //     UPDATE node
    //     SET name = '{$nodes['name']}', location = '{$nodes['location']}', id_hardware_node = {$nodes['id_hardware_node']},
    //     is_public = CAST({$isPublic} AS BOOLEAN), id_hardware_sensor = '{$convArraySensor}', field_sensor = '{$convArrayField}'
    //     WHERE id_node = {$id}
    // ";

    // $rows = app('db')->statement($sql);

                // dd($update);
        
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
