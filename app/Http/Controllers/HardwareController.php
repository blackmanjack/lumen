<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Sensor;
use App\Models\Node;
use App\Models\Hardware;
use Illuminate\Http\Request;

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
        $data = Hardware::all();
        //get all user's hardware
        return response($data);
    }

    public function showDetailData($id)
    {
        $data = Hardware::where('id', $id)->first();
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
        //only accept headers application/x-www-form-urlencoded
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded"){
            $message = "Supported format: application/x-www-form-urlencoded";
            return response()->json($message, 415);
        }

        //check input field is empty
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'description' => 'required'
        ]);

        $data = Hardware::find($id);
        
        if(strtolower($request->type) === 'single-board computer' || strtolower($request->type) === 'microcontroller unit' || strtolower($request->type) === 'sensor'){
            $update = $data->update([
                'name'=> $request->name,
                'type'=> $request->type,
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
        $data = Hardware::where('id', $id)
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
