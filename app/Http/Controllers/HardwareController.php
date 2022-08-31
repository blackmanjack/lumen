<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
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
        // add Type must Single-Board Computer, Microcontroller Unit, or Sensor
        $data = new Hardware();
        $data->name = $request->name;
        $data->type = $request->type;
        $data->description = $request->description;
        $save = $data->save();

        if($save){
            $message = "Success add new hardware";
            return response()->json($message, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Hardware::all();
        //get all user's hardware
        $response=
            // 'data'=> $data
            $data
        ;
        return response($response);
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

        $data = Hardware::find($id);
        // add Type must Single-Board Computer, Microcontroller Unit, or Sensor
        // return response($request);
        $update = $data->update([
            'name'=> $request->name,
            'type'=> $request->type,
            'description'=> $request->description,
        ]);

        if($update){
            $message = "Success edit Hardware";
            return response()->json($message, 200);
        }else{
            $message = "Empty Request Body";
            return response()->json($message, 400);
        }
        return response($response);
    }

    public function delete($id)
    {
        $data = Hardware::find($id);
        $delete = $data->delete();

        if($delete){
            $message = "Success delete Hardware, id: $id";
            return response()->json($message, 200);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
        // add Can\'t delete, hardware is still used
    }
    //
}
