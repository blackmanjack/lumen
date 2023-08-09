<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Sensor;
use App\Models\User;
use App\Models\Node;
use App\Models\Hardware;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class HardwareController extends Controller
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
        //check input field is empty
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'description' => 'required'
        ]);
        //check admin role
        $username = $request->getUser();
        $isAdmin = DB::table('user_person')->where('username', $username)
                                        ->pluck('isadmin')
                                        ->first();

        if(!$isAdmin){
            $message = "You are not admin";
            return response()->json($message, 403);
        }
        
        $data = new Hardware();
        $data->name = $request->name;
        $data->type = strtolower($request->type);
        $data->description = $request->description;
        
        if($data->type === 'single-board computer' || $data->type === 'microcontroller unit' || $data->type === 'sensor'){
            $data->save();
            $message = "Success add new hardware";
            return response()->json($message, 201);
        }else{
            $message = "Type must Single-Board Computer, Microcontroller Unit, or Sensor";
            return response()->json($message, 400);
        }
    }

    public function showAll()
    {
        $data = Hardware::select('hardware.*')->get();
        return response($data);
    }

    public function showDetailData($id)
    {
        $data = Hardware::where('id_hardware', $id)->first();

        if($data){
            return response()->json($data, 200);
        }

        else{
            $message = "Not found";
            return response()->json($message, 404);
        }
    }

    public function update(Request $request, $id)
    {
        //check admin role
        $username = $request->getUser();
        $isAdmin = DB::table('user_person')->where('username', $username)
                                        ->pluck('isadmin')
                                        ->first();

        if(!$isAdmin){
            $message = "You are not admin";
            return response()->json($message, 403);
        }
        //check hardware
        $data = Hardware::where('id_hardware', $id)->first();
        if($data == null){
            $message = "Hardware Not Found";
            return response()->json($message, 404);
        }
        
        //only accept headers application/x-www-form-urlencoded & application/json
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded" && $split !== "application/json"){
            $message = "Content-Type ".$split." Not Support, only accept application/x-www-form-urlencoded & application/json";
            return response()->json($message, 415);
        }

        //check input field is empty
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'description' => 'required'
        ]);

        $lowerType = strtolower($request->type);

        if($lowerType === 'single-board computer' || $lowerType === 'microcontroller unit' || $lowerType === 'sensor'){
            $update = $data->update([
                'name'=> $request->name,
                'type'=> $lowerType,
                'description'=> $request->description,
            ]);
            $message = "Success edit Hardware";
            return response()->json($message, 200);
        }else{
            $message = "Type must Single-Board Computer, Microcontroller Unit, or Sensor";
            return response()->json($message, 400);
        }
    }

    public function delete($id)
    {
        //check admin role
        $username = $request->getUser();
        $isAdmin = DB::table('user_person')->where('username', $username)
                                        ->pluck('isadmin')
                                        ->first();

        if(!$isAdmin){
            $message = "You are not admin";
            return response()->json($message, 403);
        }

        $data = Hardware::where('id_hardware', $id)
        ->with('Node', 'Sensor')
        ->first();

        //if data found in db
        if($data){
            //if hardware not found in node's table and sensor's table, delete hardware
            if($data->toArray()['node'] === [] && $data->toArray()['sensor'] === []){
                $data = Hardware::find($id);
                $data->delete();
                $message = 'Delete hardware, id:'.$id;
                return response()->json($message, 200);
            }else{
                $message = 'Can\'t delete, hardware is still used';
                return response()->json($message, 400);
            }
        }else{
            $message = 'Id hardware not found';
            return response()->json($message, 404);
        }
        
    }
}
