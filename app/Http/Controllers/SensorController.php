<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
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
            'node_id' => 'required',
            'hardware_id' => 'required',
            'name' => 'required',
            'unit' => 'required'
        ]);

        $data = new Sensor();
        $data->node_id = $request->node_id;
        $data->hardware_id = $request->hardware_id;
        $data->name = $request->name;
        $data->unit = $request->unit;
        $save = $data->save();

        if($save){
            $message = "Success add new sensor";
            return response()->json($message, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Sensor::all();
        // $response=
        //     // 'data'=> $data
        //     $data
        // ;
        return response($data);
    }

    public function showDetailData($id)
    {
        //query node, hardware, channel 
        $data = Sensor::where('id', $id)->with('Node', 'Channel')->get();
        //add You can\'t see another user\'s sensor
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

        //validation input
        $this->validate($request, [
            'name' => 'required',
            'unit' => 'required'
        ]);

        $data = Sensor::find($id);

        $update = $data->update([
            'name'=> $request->name,
            'unit'=> $request->unit,
        ]);

         if($update){
            $message = "Success edit Sensor";
            return response()->json($message, 200);
        }else{
            $message = "Empty Request Body";
            return response()->json($message, 400);
        }
        return response($response);
    }

    public function delete($id)
    {
        $data = Sensor::find($id);
        $delete = $data->delete();

        if($delete){
            $message = "Success delete Sensor, id: $id";
            return response()->json($message, 200);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
        // return response($response);
    }
    //
}
