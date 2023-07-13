<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Sensor;
use App\Models\User;
use App\Models\Node;
use App\Models\Hardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        try{
            //check input field is empty
            $this->validate($request, [
                'name' => 'required|string|max:256',
                'type' => 'required|string|max:256',
                'description' => 'required|string|max:256'
            ]);

            $userId = Auth::id();
            
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
        }catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function showAll()
    {
        try{
            $data = Hardware::select('hardware.*')  
                ->with('Sensor','Node')
                ->get();
            return response($data);
        }catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function showDetailData($id)
    {
        try{
            $id_user = Auth::id();
    
            $data = Hardware::where('id_hardware', $id)->with('Node', 'Sensor')->first();

            if($data){
                return response()->json($data, 200);
            }else{
                $message = "Not found";
                return response()->json($message, 404);
            }
        }catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error' => 'Invalid Input'], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $data = Hardware::where('id_hardware', $id)->first();
            if($data == null){
                $message = "Hardware Not Found";
                return response()->json($message, 404);
            }
            //only accept headers application/x-www-form-urlencoded
            $contentType = $request->headers->get('Content-Type');
            $split = explode(';', $contentType)[0];
            if($split !== "application/x-www-form-urlencoded"){
                $message = "Supported format: application/x-www-form-urlencoded";
                return response()->json($message, 415);
            }
    
            //check input field is empty
            $this->validate($request, [
                'name' => 'required|string|max:256',
                'type' => 'required|string|max:256',
                'description' => 'required|string|max:256'
            ]);
    
            if($request->type === 'single-board computer' || $request->type === 'microcontroller unit' || $request->type === 'sensor'){
                $update = $data->update([
                    'name'=> $request->name,
                    'type'=> strtolower($request->type),
                    'description'=> $request->description,
                ]);
                $message = "Success edit Hardware";
                return response()->json($message, 200);
            }else{
                $message = "Type must Single-Board Computer, Microcontroller Unit, or Sensor";
                return response()->json($message, 400);
            }
        }catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error' => 'Invalid Input'], 400);
        }
    }

    public function delete($id)
    {
        try{
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
        }catch (\Illuminate\Database\QueryException $exception) {
            return response()->json(['error' => 'Invalid Input'], 400);
        }
    }
}
