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
    // $authentication = check_token($request);
    // if ($authentication[0]) {
    //     $authtoken = $authentication[1];
    //     $node = $request->json()->all();

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
            // $validator = Validator::make($node, [
            //     'name' => 'required',
            //     'location' => 'required',
            //     'id_hardware_node' => 'required|integer',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'description' => 'Bad Request',
            //         'status' => 400,
            //         'message' => $validator->errors()->first(),
            //     ], 400);
            // }

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
            // dd(gettype($arraysensor), $arraysensor);
            // $dt_sensor = explode(',', str_replace(['{', '}'], '', $arraysensor));
            foreach ($arraysensor as $x) {
                if ($x !== 'NULL') {
                    // dd($x);
                    // $query = "SELECT hardware.type FROM hardware WHERE id_hardware = :x";
                    // $rows = DB::select($query, ['x' => $x]);
                    // $query = "SELECT hardware.type FROM hardware WHERE id_hardware = :idHardware";
                    // $rows = DB::select($query, ['idHardware' => $x]);
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
        // dd($arraysensor);

        // try {
        //     // VALIDATE id_hardware_sensor exist and should be type sensor
        //     $arraySensor = isset($node['id_hardware_sensor']) ? $node['id_hardware_sensor'] : [];
        //     // dd($arraySensor);
        //     foreach ($arraySensor as $sensorId) {
        //         if ($sensorId !== null) {
        //             $hardwareType = DB::table('hardware')
        //                 ->select('type')
        //                 ->where('id_hardware', $sensorId)
        //                 ->value('type');

        //             if (empty($hardwareType)) {
        //                 return response()->json([
        //                     'description' => 'Bad Request',
        //                     'status' => 400,
        //                     'message' => 'Id hardware for sensor not found. id = ' . $sensorId,
        //                 ], 400);
        //             }

        //             if ($hardwareType != 'sensor') {
        //                 return response()->json([
        //                     'description' => 'Bad Request',
        //                     'status' => 400,
        //                     'message' => 'Hardware sensor type not match, type should be sensor. id = ' . $sensorId,
        //                 ], 400);
        //             }
        //         }
        //     }
        //     // dd($arraySensor);
        // } catch (\Exception $e) {
        //     $arraySensor = [];
        // }

        // try {
        //     // VALIDATE the field should be registered on existed sensor column
        //     $arrayField = isset($node['field_sensor']) ? $node['field_sensor'] : [];
        //     // dd($arrayField);
        //     foreach ($arrayField as $index => $field) {
        //         // dd($arrayField);
        //         if ($field !== null) {
        //             if (!isset($arraySensor[$index])) {
        //             // if ($arraySensor[$index] === null) {
        //                 return response()->json([
        //                     'description' => 'Bad Request',
        //                     'status' => 400,
        //                     'message' => 'Field sensor is empty. field = ' . $index,
        //                 ], 400);
        //             }
        //         }
        //     }
        //     // dd($arrayField);
        // } catch (\Exception $e) {
        //     $arrayField = [];
        // }

        // dd($arrayField);

        // VALIDATE the field should be registered on existed sensor column
        $arrayField = isset($node['field_sensor']) ? $node['field_sensor'] : [];

        if (isset($node['field_sensor'])) {
            $arrayfield = $node['field_sensor'];
            // dd(implode(',', $arrayfield));
            // dd($arrayfield);
            // $dt_field = $arrayfield;
            // $dt_field = explode(',', str_replace(['{', '}'], '', $arrayfield));
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

        // foreach ($array as $index => $field) {
        //     if ($field !== null) {
        //         if (!isset($arraySensor[$index])) {
        //             dd(!isset($arraySensor[$index]), $arraySensor[$index] === null);
        //         // if ($arraySensor[$index] === null) {
        //             return response()->json([
        //                 'description' => 'Bad Request',
        //                 'status' => 400,
        //                 'message' => 'Field sensor is empty. field = ' . $index,
        //             ], 400);
        //         }
        //     }
        // }

        // $arraySensor = array_map(function ($value) {
        //     return $value === null ? 'NULL' : $value;
        // }, $arraysensor);
        
        // $arrayField = array_map(function ($value) {
        //     return $value === null ? 'NULL' : $value;
        // }, $arrayfield);
        $id_user = Auth::id();
        // dd($node['field_sensor'], $node['id_hardware_sensor']);
        // dd($arraysensor, $arrayfield);
        // dd(json_encode($arraySensor),json_encode($arrayField));
        // dd('hai');
        // dd(gettype($arraysensor));
        // INSERT value
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
    // } else {
    //     return response()->json([
    //         'description' => 'Forbidden',
    //         'status' => 403,
    //         'message' => 'You are unauthorized, invalid token.',
    //     ], 403);
    // }
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
        ->with('User','Hardware')
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
        if($split !== "application/x-www-form-urlencoded"){
            $message = "Supported format: application/x-www-form-urlencoded";
            return response()->json($message, 415);
        }
        
        $userid = Auth::id();

        $this->validate($request, [
            'name' => 'required',
            'location' => 'required',
        ]);

        $findNode = Node::where('id_node', $id)->first();

        $CheckuserID = Node::where('id_node', $id)->pluck('id_user')->first();

        $node = Node::where('id_user', $userid)->where('id_node', $id)->first(); 

        if($findNode){
            if($node && $CheckuserID == $userid){
                $update = $node->update([
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
